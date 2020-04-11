<?php

namespace DBSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class UpgradeController extends Controller
{
    protected $newVersion = null;

    public function __construct()
    {
        $this->newVersion = check_new_version();
    }

    /**
     * Get the upgrade page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUpgradePage()
    {
        $version = $this->newVersion ?? null;

        return view('upgrade.index')->with(compact('version'));
    }

    /**
     * Run the upgrade
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function upgrade(Request $request)
    {
        if (!$this->newVersion) {
            return redirect('upgrade')
                ->route('upgrade')
                ->withErrors(['Application upgrade failed']);
        }

        // make migrations
        Artisan::call('migrate', ['--force' => true]);

        // make a backup of .env file
        Storage::disk('base')->copy('.env', 'storage/old/.env_' . date('YmdHis'));

        $this->setNewVersion();

        // cache the config
        app('files')->put(
            app()->getCachedConfigPath(), '<?php return '.var_export(config()->all(), true).';'.PHP_EOL
        );

        // remove install folder if present
        if (is_dir(public_path('install'))) {
            @unlink(public_path('install'));
        }

        Storage::disk('base')->delete('.env.example');

        $request->session()->flash('message', 'Application upgraded to version ' . $this->newVersion);

        return redirect()->route('upgrade');
    }

    /**
     * Update the .env file with the new version
     */
    protected function setNewVersion()
    {
        config(['app.version' => $this->newVersion]);

        $env = file_get_contents(base_path('.env'));
        $env = preg_replace("/(.*?APP_VERSION=).*?(.+?)\\n/msi", '${1}' . $this->newVersion . "\n", $env);

        file_put_contents(base_path('.env') , $env);
    }
}
