<?php

namespace DBSystem\Http\Controllers;

use DBSystem\Backup;
use DBSystem\Database;
use DBSystem\Destination;
use DBSystem\Dumpers\Files;
use DBSystem\Mail\BackupTransfer;
use DBSystem\Tasks\Restore;
use DBSystem\Traits\HandlesBackups;
use DBSystem\Traits\HandlesDatabases;
use DBSystem\Traits\HasDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Flysystem\FileNotFoundException;

class BackupController extends Controller
{
    use HandlesDatabases, HasDataTable, HandlesBackups;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Backup::query();

        if ($request->query('user')) {
            $query = $query->where('user_id', $request->query('user'));
        }

        if ($request->query('task')) {
            $query = $query->where('task_id', $request->query('task'));
        }

        if ($request->query('type')) {
            $query = $query->whereType($request->query('type'));
        }

        if ($request->query('storage')) {
            if ($request->query('storage') === 'local') {
                $query = $query->where('saved_locally', 1);
            }

            $query = $query->orWhereHas('destinations', function ($query) use ($request) {
                $query->whereHas('destination', function ($query) use ($request) {
                    $query->whereDriver($request->query('storage'));
                });
            });
        }

        $data = $this->getDataTable($query->with('destinations.destination'), $request);

        return response()->json($data);
    }

    /**
     * Download backup file
     *
     * @param Backup $backup
     * @return mixed
     */
    public function download(Backup $backup)
    {
        if (!Storage::disk('backups')->has($backup->name)) {
            return response()->make('Backup file not found');
        }

        return Storage::disk('backups')->download($backup->name);
    }

    /**
     * Transfer backup file to other destinations
     *
     * @param Request $request
     * @param Backup $backup
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function transfer(Request $request, Backup $backup)
    {
        $data = $request->validate([
            'destination' => 'required_if:send_to_email,false|nullable|array',
            'destination.id' => 'required_with:destination',
            'path' => 'nullable|string',
            'save' => 'nullable|boolean',
            'send_to_email' => 'required_without:destination|boolean',
            'email' => 'required_if:send_to_email,true|nullable|email',
        ]);

        $msg = 'Backup successfully uploaded';
        $destinations = [];
        if (isset($data['destination']) && isset($data['destination']['id'])) {
            $destination = Destination::findOrFail($data['destination']['id']);
            $path = $data['path'] ? $data['path'] : '';
            $backupDestinationPath = '';
            if ($destination->driver === 'local') {
                $backupDestinationPath = $data['path'];
            } else {
                if (isset($data['path']) && $data['path']) {
                    $destination->root = $data['path'];
                }
            }

            $this->uploadToDestination(
                $destination,
                $backupDestinationPath . $backup->name, Storage::disk('backups')->get($backup->name)
            );

            if ($request->input('save')) {
                $backup->destinations()->create([
                    'destination_id' => $destination->id,
                    'path' => $path,
                ]);
            }

            $destinations[] = $destination->name;
            $msg .= " to {$destination->name}";
        }

        if (isset($data['email']) && $data['email']) {
            $destinations[] = $data['email'];
            Mail::to($data['email'])->send(new BackupTransfer($backup));
        }

        // register the activity
        activity()
            ->by(auth()->user())
            ->on($backup)
            ->withProperty('to', $destinations)
            ->log('transfered');

        return response()->json([
            'message' => $msg
        ]);
    }

    /**
     * Import backups
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimetypes:application/zip,application/x-tar,application/x-gzip',
            'backup_types' => 'required|array',
        ]);

        $i = 0;
        $backups = [];
        foreach ($request->file('files') as $file) {
            Backup::create([
                'user_id' => auth()->user()->id,
                'name' => $file->getClientOriginalName(),
                'type' => $request->input('backup_types')[$i],
                'size' => $file->getClientSize(),
            ]);

            $backups[] = $file->getClientOriginalName();
            $file->storeAs('', $file->getClientOriginalName(), 'backups');
            $i++;
        }

        // register the activity
        activity()
            ->by(auth()->user())
            ->withProperty(count($backups) > 1 ? 'backups' : 'backup', $backups)
            ->log('imported');

        return response()->json([
            'message' => "Backups successfully imported"
        ]);
    }

    /**
     * Delete multiple backups
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function bulkDeleteAction(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required',
        ]);

        $backups = Backup::whereIn('id', collect($data['items'])
            ->pluck('id')->toArray())->get();

        $this->bulkDelete($backups->toArray());

        return response()->json([
            'message' => "Backups successfully deleted"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Backup $backup
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Request $request, Backup $backup)
    {
        $data = $request->validate([
            'delete_local' => 'required|boolean',
            'destinations' => 'required_without:delete_local|array',
            'destinations.*.id' => 'required',
        ]);

        $backup->disableLogging();

        $msg = "";
        $warnings = [];
        $deletedFrom = [];

        if (isset($data['destinations']) && count($data['destinations'])) {
            $destinationsIds = collect($data['destinations'])->pluck('id');
            $backup->load(['destinations' => function ($query) use ($destinationsIds) {
                $query->whereIn('id', $destinationsIds)->with('destination');
            }]);

            foreach ($backup->destinations as $backupDestination) {
                if ($backupDestination->path) {
                    $backupDestination->destination->root = $backupDestination->path;
                }

                try {
                    $this->deleteFromDestination($backupDestination->destination, $backup->name);
                } catch (FileNotFoundException $exception) {
                    $warnings[] = "File not found at destination {$backupDestination->destination->name}" .
                        ($backupDestination->path ? ' for path ' . $backupDestination->path : '');
                } finally {
                    $deletedFrom[] = $backupDestination->destination->name;
                    $backupDestination->delete();
                }
            }

            $msg = "Backup successfully deleted from destinations";
        }

        if (isset($data['delete_local']) && $data['delete_local']) {
            Storage::disk('backups')->delete($backup->name);
            $deletedFrom[] = 'local disk';

            if ($backup->destinations()->count()) {
                $backup->saved_locally = false;
                $backup->save();
            } else {
                $backup->delete();
            }

            $msg = "Backup successfully deleted";
        }

        // register the activity
        activity()
            ->by(auth()->user())
            ->on($backup)
            ->withProperty('from', $deletedFrom)
            ->log('deleted');

        return response()->json([
            'message' => $msg,
            'warnings' => $warnings
        ]);
    }

    /**
     * Restore a backup
     *
     * @param Request $request
     * @param Backup $backup
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function restore(Request $request, Backup $backup)
    {
        $data = $request->validate([
            'databases' => 'required_without_all:files,self_backup|array',
            'databases.*.archive_path' => 'required|string',
            'databases.*.database' => 'required|array',
            'files' => 'required_without_all:databases,self_backup|array',
            'files.*.archive_path' => 'required|string',
            'files.*.destination_path' => 'required|string',
            'self_backup' => 'nullable|boolean',
            'destination_path' => 'nullable|required_if:self_backup,true|string',
        ]);

        $file = Storage::disk('backups')->path($backup->name);

        if ($data['self_backup']) {
            $dumper = new Files();
            $dumper->restoreFromFile($file, base_path($data['destination_path']));
        } else {
            $restore = new Restore($file);

            if (count($data['databases'])) {
                foreach ($data['databases'] as $item) {
                    $database = Database::findOrFail($item['database']['id']);
                    $restore->addDatabase($this->getDatabaseDumper($database), $item['archive_path']);
                }
            }

            if (count($data['files'])) {
                foreach ($data['files'] as $item) {
                    $dumper = new Files();
                    $restore->addFiles($dumper, $item['archive_path'], base_path($item['destination_path']));
                }
            }

            $restore->execute();
        }

        // register the activity
        activity()
            ->by(auth()->user())
            ->on($backup)
            ->log('restored');

        return response()->json([
            'message' => 'Backup successfully restored'
        ]);
    }

    /**
     * Extract and parse the backup manifest
     *
     * @param Backup $backup
     * @return \Illuminate\Http\JsonResponse
     */
    public function getManifest(Backup $backup)
    {
        $file = Storage::disk('backups')->path($backup->name);

        $manifest = get_archive_file($file, 'manifest.json');
        $manifest = json_decode($manifest, true);
        $manifest = $this->validateManifest($manifest) ? $manifest : [];

        return response()->json($manifest);
    }

    protected function validateManifest($manifest)
    {
        if (!is_array($manifest)) {
            return false;
        }

        $validator = Validator::make($manifest, [
            'databases' => 'required_without:files|array',
            'files' => 'required_without:databases|array',
        ]);

        return !$validator->fails();
    }
}
