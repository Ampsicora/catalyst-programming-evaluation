<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;


class Connection
{
    protected Capsule $capsule;


    public function __construct(array $config)
    {
        $this->addConnection($config);

        $this->capsule->setAsGlobal();

        $this->capsule->bootEloquent();
    }


    public function addConnection(array $config)
    {
        $this->capsule = new Capsule;

        $this->capsule->addConnection([
            'driver'    => $config['driver'],
            'host'      => $config['host'],
            'database'  => $config['database'],
            'username'  => $config['username'],
            'password'  => $config['password']
        ]);
    }
}
