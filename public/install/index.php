<?php

use DBSystem\User;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

final class Installer
{
    protected $appVersion;

    protected $installPath = '';

    /**
     * @param $path
     * @return $this
     * @throws Exception
     */
    public function setInstallationPath(string $path)
    {
        $this->installPath = ltrim($path, '/\\') . DIRECTORY_SEPARATOR;

        if (!is_dir($this->installPath) || !file_exists($this->installPath . 'vendor/autoload.php')) {
            throw new Exception('The installation path is not valid');
        }

        return $this;
    }

    /**
     * Run the installation
     *
     * @throws Exception
     */
    public function run()
    {
        if (!file_exists($this->installPath . '.env.example')) {
            throw new Exception('Installation failed, missing .env.example file');
        }

        // extract app version
        $env = file_get_contents($this->installPath . '.env.example');
        preg_match("/(.*?APP_VERSION=).*?(.+?)\\n/msi", $env, $matches);
        $this->appVersion = $matches[2];

        // create the .env file
        $this->createEnvFile();

        // boot the framework
        require $this->installPath . 'vendor/autoload.php';
        $app = require $this->installPath . 'bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        Artisan::call('config:clear');
        Artisan::call('key:generate', ['--force' => true]);
        config(['app.url' => rtrim(url(''), '/')]);

        // cache the config
        $app['files']->put(
            $app->getCachedConfigPath(), '<?php return '.var_export($app['config']->all(), true).';'.PHP_EOL
        );

        // migrate the database tables
        Artisan::call('migrate:fresh', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);

        User::create([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => bcrypt($_POST['password']),
        ]);

        @unlink($this->installPath . '.env.example');
    }

    /**
     * Generate the .env file
     */
    protected function createEnvFile()
    {
        copy($this->installPath . '.env.example', $this->installPath . '.env');

        $env =
            'APP_NAME=DBSystem' . PHP_EOL .
            'APP_ENV=production' . PHP_EOL .
            'APP_KEY=' . '' . PHP_EOL .
            'APP_DEBUG=false' . PHP_EOL .
            'APP_URL=' . '' . PHP_EOL .
            'APP_TIMEZONE=' . $_POST['timezone'] . PHP_EOL .
            'APP_VERSION=' . $this->appVersion . PHP_EOL.PHP_EOL .
            'DB_CONNECTION=' . $_POST['db_connection'] . PHP_EOL .
            'DB_HOST=' . $_POST['db_host'] . PHP_EOL .
            'DB_PORT=' . (isset($_POST['db_port']) ? $_POST['db_port'] : 3306) . PHP_EOL .
            'DB_DATABASE=' . $_POST['db_name'] . PHP_EOL .
            'DB_USERNAME=' . (isset($_POST['db_username']) ? $_POST['db_username'] : '') . PHP_EOL .
            'DB_PASSWORD=' . (isset($_POST['db_password']) ? $_POST['db_password'] : '') . PHP_EOL .
            'DB_PREFIX=dbs_' . PHP_EOL.PHP_EOL .
            'BROADCAST_DRIVER=' . (isset($_POST['broadcast_driver']) ? $_POST['broadcast_driver'] : 'log') . PHP_EOL .
            'CACHE_DRIVER=' . (isset($_POST['cache_driver']) ? $_POST['cache_driver'] : 'file') . PHP_EOL .
            'SESSION_DRIVER=' . (isset($_POST['session_driver']) ? $_POST['session_driver'] : 'file') . PHP_EOL .
            'QUEUE_DRIVER=' . (isset($_POST['queue_driver']) ? $_POST['queue_driver'] : 'sync') . PHP_EOL.PHP_EOL .
            'REDIS_HOST=' . (isset($_POST['redis_host']) ? $_POST['redis_host'] : '127.0.0.1') . PHP_EOL .
            'REDIS_PASSWORD=' . (isset($_POST['redis_password']) ? $_POST['redis_password'] : 'null') . PHP_EOL .
            'REDIS_PORT=' . (isset($_POST['redis_port']) ? $_POST['redis_port'] : '6379') . PHP_EOL.PHP_EOL .
            'MAIL_DRIVER=' . $_POST['mail_driver'] . PHP_EOL .
            'MAIL_HOST=' . $_POST['mail_host'] . PHP_EOL .
            'MAIL_PORT=' . $_POST['mail_port'] . PHP_EOL .
            'MAIL_USERNAME=' . $_POST['mail_username'] . PHP_EOL .
            'MAIL_PASSWORD=' . $_POST['mail_password'] . PHP_EOL .
            'MAIL_ENCRYPTION=' . (isset($_POST['mail_encryption']) ? $_POST['mail_encryption'] : 'tls') . PHP_EOL.PHP_EOL .
            'MAIL_FROM_ADDRESS=' . $_POST['mail_from_address'] . PHP_EOL .
            'MAIL_FROM_NAME=' . $_POST['mail_from_name'] . PHP_EOL.PHP_EOL .
            'BACKUPS_DIRECTORY=' . $_POST['backups_directory'] . PHP_EOL;

        file_put_contents($this->installPath . '.env', $env);
    }
}

if (isset($_POST['installation_path'])) {

    try {
        @set_time_limit(120);

        if ($_POST['password'] !== $_POST['password_confirm']) {
            throw new Exception('User passwords do not match');
        }

        $installer = new Installer();
        $installer->setInstallationPath($_POST['installation_path'])->run();

        $installed = true;
    } catch (Exception $exception) {
        $error = $exception->getMessage();
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DBSystem - Installation</title>
    <link rel="shortcut icon" href="../favicon.ico">
    <link href="css/bulma.min.css" rel="stylesheet">
    <link href="css/bulma-steps.min.css" rel="stylesheet">
</head>
<body>
<section class="hero is-light is-fullheight">
    <div class="hero-body">
        <div class="container is-fluid">
            <div class="columns is-centered is-vcentered">
                <div class="column is-7">
                    <div class="content has-text-centered">
                        <p>
                            <img alt="Logo" src="../img/logo-square.svg" style="width: 75px;">
                        </p>
                        <h1 class="title is-5 has-text-link">Installation</h1>
                    </div>

                    <div class="card" style="margin-bottom: 1rem;">
                        <div class="card-content">
                            <?php if (isset($error) && $error) { ?>
                                <article class="message is-danger">
                                    <div class="message-body">
                                        Installation failed with following error:
                                        <br>
                                        <code><?php echo $error ?></code>
                                    </div>
                                </article>
                            <?php } ?>
                            <?php if (isset($installed) && $installed) { ?>
                                <article class="message is-success">
                                    <div class="message-body">
                                        Application successfully installed.
                                    </div>
                                </article>
                                <article class="message is-warning">
                                    <div class="message-body">
                                        Don't forget to delete the install folder!
                                    </div>
                                </article>
                                <div class="field">
                                    <div class="control has-text-centered">
                                        <a href="../" class="button is-link">Login</a>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <form class="form-horizontal" method="post">
                                    <div class="steps" id="installSteps">
                                        <div class="step-item is-active is-success">
                                            <div class="step-marker">1</div>
                                            <div class="step-details">
                                                <p class="step-title">App</p>
                                            </div>
                                        </div>
                                        <div class="step-item">
                                            <div class="step-marker">2</div>
                                            <div class="step-details">
                                                <p class="step-title">Database</p>
                                            </div>
                                        </div>
                                        <div class="step-item">
                                            <div class="step-marker">3</div>
                                            <div class="step-details">
                                                <p class="step-title">Mail</p>
                                            </div>
                                        </div>
                                        <div class="step-item">
                                            <div class="step-marker">4</div>
                                            <div class="step-details">
                                                <p class="step-title">Account</p>
                                            </div>
                                        </div>
                                        <div class="steps-content">
                                            <div class="step-content is-active">
                                                <div class="field">
                                                    <label class="label">Install path <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="text" value="../../" name="installation_path"
                                                               placeholder="Path to you application folder" required>
                                                    </div>
                                                    <p class="help">This is the path to application source files.</p>
                                                </div>
                                                <hr>
                                                <div class="field">
                                                    <label class="label">Backups directory <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="text" name="backups_directory" value="backups"
                                                               placeholder="The directory where to store the backups" required>
                                                    </div>
                                                    <p class="help">The path is relative to your app storage folder.</p>
                                                </div>
                                                <hr>
                                                <div class="field">
                                                    <label class="label">Timezone <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <div class="select is-fullwidth">
                                                            <select name="timezone" required>
                                                                <?php foreach (timezone_identifiers_list() as $tz) { ?>
                                                                    <option value="<?php echo $tz; ?>"><?php echo $tz; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="step-content">
                                                <div class="field">
                                                    <label class="label">Database driver <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <div class="select is-fullwidth">
                                                            <select name="db_connection" required>
                                                                <option selected value="mysql">MySql</option>
                                                                <option value="pgsql">PostgreSql</option>
                                                                <option value="sqlite">Sqlite</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Database host <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="text" name="db_host" value="127.0.0.1" placeholder="Database host" required>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Database port <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="text" name="db_port" value="3306" placeholder="Database port" required>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Database name <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="text" name="db_name" placeholder="The name of your database" required>
                                                    </div>
                                                    <p class="help">If the database is sqlite, specify the full path to the file.</p>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Database user <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="text" name="db_username" value="root" placeholder="Database user" required>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Database password</label>
                                                    <div class="control">
                                                        <input class="input" type="password" name="db_password" placeholder="Database password">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="step-content">
                                                <div class="field">
                                                    <label class="label">Mail driver <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <div class="select is-fullwidth">
                                                            <select name="mail_driver" required>
                                                                <option selected value="smtp">Smtp</option>
                                                                <option value="sendmail">Sendmail</option>
                                                                <option value="mailgun">Mailgun</option>
                                                                <option value="mandrill">Mandrill</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Mail host <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="text" name="mail_host" placeholder="Mail host ex. smtp.mailtrap.io" required>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Mail port <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="text" name="mail_port" value="587" placeholder="Mail port" required>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Mail username <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="text" name="mail_username" placeholder="Mail username" required>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Mail password <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="password" name="mail_password" placeholder="Mail password" required>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Global mail from address <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="email" name="mail_from_address" placeholder="hello@example.com" required>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Global mail from name <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="text" name="mail_from_name" placeholder="Example" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="step-content">
                                                <div class="field">
                                                    <label class="label">User name <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="text" name="name" placeholder="A name for the user" required>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">User email <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="email" name="email" placeholder="A valid email" required>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">User password <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="password" name="password" placeholder="Your password" required>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label class="label">Confirm Password <span class="has-text-danger">*</span></label>
                                                    <div class="control">
                                                        <input class="input" type="password" name="password_confirm" placeholder="Write password again" required>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="field">
                                                    <div class="control has-text-centered">
                                                        <button type="submit" class="button is-success">Install</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="steps-actions">
                                            <div class="steps-action">
                                                <a href="#" data-nav="previous" class="button is-light">Previous</a>
                                            </div>
                                            <div class="steps-action">
                                                <a href="#" data-nav="next" class="button is-light">Next</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                    <p class="has-text-centered has-text-grey">&copy; DBSystem</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="js/bulma-steps.min.js"></script>
<script>
    window.addEventListener('load', function() {
        bulmaSteps.attach('#installSteps');

        if (window.Intl) {
            var tzSelect = document.querySelector('select[name="timezone"]');
            tzSelect.value = Intl.DateTimeFormat().resolvedOptions().timeZone;
        }
    })
</script>
</body>
</html>

