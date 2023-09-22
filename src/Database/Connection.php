<?php

declare(strict_types=1);

namespace App\Database;

use Illuminate\Database\Capsule\Manager as Capsule;



class Connection
{
    public static function connect(array $config)
    {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => $config['driver'],
            'host'      => $config['host'],
            'database'  => $config['database'],
            'username'  => $config['username'],
            'password'  => $config['password'],
        ]);

        $capsule->setAsGlobal();

        $capsule->bootEloquent();
    }
}
