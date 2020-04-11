<?php

return [
    "mysql" => [
        "database"  => 'sakila',
        "database2" => 'test',
        "bin"       => 'C:\xampp7\mysql\bin',
        "user"      => 'root',
        "pass"      => '',
    ],

    "sqlite" => [
        "database"  => 'tests/test.sqlite',
        "database2" => 'tests/samples/test.sqlite.sql',
    ],

    "files" => [
        "path"      => 'tests/samples',
        "backup"    => 'tests/samples/backup.zip'
    ],

    "ftp" => [
        'host'     => '',
        'username' => '',
        'password' => ''
    ],

    "dropbox" => [
        'token'    => ''
    ],

    "googledrive" => [
        'client_id'     => '',
        'client_secret' => '',
        'refresh_token' => '',
    ],
];