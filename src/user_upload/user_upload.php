<?php

declare(strict_types=1);

require '../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Reader;


class UserUpload
{
    protected $dryRun;
    protected $createTable;


    public function __construct(bool $dryRun, bool $createTable)
    {
        $this->dryRun = $dryRun;
        $this->createTable = $createTable;
    }
}

try {
    $options = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);

    optionsCheck($options);

    $dryRun         = isset($options['dry_run']);
    $createTable    = isset($options['create_table']);

    $database = new Connection([
        'driver'    => 'mysql',
        'host'      => $options['h'],
        'database'  => 'catalyst',
        'username'  => $options['u'],
        'password'  => $options['p'],
    ]);

    $userUpload = new UserUpload($dryRun, $createTable);
} catch (\Throwable $e) {
    echo $e->getMessage();
    exit(1);
}
