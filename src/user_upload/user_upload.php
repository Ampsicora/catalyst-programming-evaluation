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

    public function addUsersTable(): void
    {
        // The table users is dropped following point 4 of script task: "The users database table will need to be created/rebuilt"
        Capsule::schema()->dropIfExists('users');

        Capsule::schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('name');
            $table->string('surname');
            $table->timestamps();
        });
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
