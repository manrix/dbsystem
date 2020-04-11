<?php

namespace Tests\Feature;

use DBSystem\Backup;
use DBSystem\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupControllerTest extends TestCase
{
    public function test_get_manifest()
    {
        DB::beginTransaction();

        $backup = Backup::create([
            'name' => 'test.zip',
            'size' => 0,
        ]);

        Storage::disk('backups')->put('test.zip', file_get_contents($this->config['files']['backup']));

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->json('GET', '/backups/' . $backup->id . '/manifest');

        $response->assertStatus(200)->assertJson([]);

        Storage::disk('backups')->delete('test.zip');

        $backup->delete();
    }

    public function test_import()
    {
        Storage::fake('backups');

        DB::beginTransaction();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->json('POST', '/backups/import', [
            'files' => [UploadedFile::fake()->create('test.zip')],
            'backup_types' => ['database'],
        ]);

        $response->assertStatus(200)->assertJson([
            'message' => "Backups successfully imported"
        ]);

        DB::commit();

        $this->assertDatabaseHas('backups', [
            'name' => 'test.zip'
        ]);

        DB::rollBack();
    }
}
