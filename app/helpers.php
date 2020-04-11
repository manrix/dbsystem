<?php

if (!function_exists('check_new_version')) {
    function check_new_version()
    {
        if (file_exists(base_path('.env.example'))) {
            $env = file_get_contents(base_path('.env.example'));
            preg_match("/(.*?APP_VERSION=).*?(.+?)\\n/msi", $env, $matches);

            if ($matches[2] !== config('app.version')) {
                return $matches[2];
            }
        }

        return false;
    }
}

if (!function_exists('get_connection_dsn')) {
    function get_connection_dsn($driver, $database, $host = 'localhost')
    {
        if ($driver != 'sqlite') {
            return sprintf("%s:host=%s;dbname=%s", $driver, $host, $database);
        } else {
            return sprintf("%s:%s", $driver, $database);
        }
    }
}

if (!function_exists('get_database_tables')) {
    function get_database_tables($driver, $database, $host, $user, $password = '')
    {
        $pdo = new \PDO(get_connection_dsn($driver, $database, $host), $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

        $tables = [];
        if ($driver === 'mysql') {
            $tables = $pdo->query("SHOW TABLES")->fetchAll();
        } elseif ($driver === 'pgsql') {
            $tables = $pdo->query("SELECT * FROM pg_catalog.pg_tables")->fetchAll();
        } elseif ($driver === 'sqlite') {
            $tables = $pdo->query("SELECT * FROM sqlite_master where type='table'")->fetchAll();
        }

        return array_map(function ($table) {
            return $table[0];
        }, $tables);
    }
}

// https://stackoverflow.com/a/13733588/1838482
if (!function_exists('generate_token')) {
    function generate_token($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }
}

if (!function_exists('format_size')) {
    function format_size($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if ($size === 0) {
            return '0 ' . $units[1];
        }

        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}

if (!function_exists('get_archive_file')) {
    function get_archive_file($archive, $file)
    {
        $extension = pathinfo($archive, PATHINFO_EXTENSION);

        switch ($extension) {
            case 'zip':
                $content = null;
                $zip = new \ZipArchive();
                if ($zip->open($archive) && $zip->locateName($file) !== false) {
                    $content = $zip->getFromName($file);
                }
                $zip->close();
                return $content;
                break;
            case 'gz':
            case 'tar':
                $phar = new \PharData($archive);
                try {
                    return $phar[$file]->getContent();
                } catch (BadMethodCallException $e) {
                    return null;
                }
                break;
            default:
                throw new \InvalidArgumentException('Archive type not supported');
                break;
        }
    }
}

if (!function_exists('settings')) {
    function settings($key = null) {
        if ($key) {
            return array_get(app('settings'), $key);
        }

        return app('settings');
    }
}