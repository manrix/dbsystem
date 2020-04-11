<?php

use Faker\Generator as Faker;

$factory->define(DBSystem\Activity::class, function (Faker $faker) {
    $resource = ['backup', 'task', 'database', 'destination', 'user'];
    $action = ['create', 'update', 'delete'];

    return [
        'resource' => $resource[array_rand($resource)],
        'action' => $action[array_rand($action)],
        'backup_id' => 1,
        'task_id' => 1,
        'database_id' => 1,
        'destination_id' => 1,
        'user_id' => 1,
    ];
});
