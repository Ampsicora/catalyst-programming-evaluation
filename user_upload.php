<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use function App\displayHelp;
use App\Database\Connection;
use App\Services\UserUpload;
use App\Services\UserRepository;


try {
    $config = require __DIR__ . '/config/database.php';
    $options = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);


    // Check for --help option
    if (isset($options['help'])) {
        displayHelp();
        exit(0);
    }

    // Check for required options
    if (!isset($options['file'], $options['u'], $options['p'], $options['h']))
        throw new Exception("Error: Missing required options. Use --help for usage information.\n", 1);


    $dryRun         = isset($options['dry_run']);
    $createTable    = isset($options['create_table']);

    Connection::connect([
        'driver'    => $config['driver'],
        'host'      => $options['h'],
        'database'  => $config['database'],
        'username'  => $options['u'],
        'password'  => $options['p'],
    ]);

    $userRepository = new UserRepository;
    $userUpload     = new UserUpload($userRepository, $dryRun);

    if ($createTable) {
        echo 'Users table created successfully';
        exit(0);
    }

    $userUpload->run($options['file']);
} catch (\Throwable $e) {
    echo $e->getMessage();
    exit(1);
}
