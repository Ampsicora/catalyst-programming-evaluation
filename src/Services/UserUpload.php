<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use League\Csv\Reader;
use App\Models\User;
use Exception;
use Iterator;

use function App\normalization;
use function App\is_email_valid;


class UserUpload
{
    protected $dryRun;
    protected $createTable;


    public function __construct(bool $dryRun, bool $createTable)
    {
        $this->dryRun = $dryRun;
        $this->createTable = $createTable;

        try {
            if (!$this->dryRun)
                $this->addUsersTable();

            if ($this->createTable) {
                echo 'Users table created successfully';
                exit(0);
            }
        } catch (\Throwable) {
            throw new Exception("Error Creating the user table");
        }
    }


    public function run(string $filepath): void
    {
        $records = $this->getRecordsFromCSV($filepath);

        foreach ($records as $record) {
            try {
                $record = normalization($record);

                if (!is_email_valid($record['email']))
                    throw new Exception("Warning: email {$record['email']} is not valid and will not be added to the database \n");

                $this->addRecordToDB($record);
            } catch (\Throwable $e) {
                echo $e->getMessage();
                continue;
            }
        }
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

    public function getRecordsFromCSV(string $filepath): Iterator
    {
        try {
            $reader = Reader::createFromPath($filepath . '.csv', 'r');

            $reader->setHeaderOffset(0);

            return $reader->getRecords(['name', 'surname', 'email']);
        } catch (\Throwable) {
            throw new Exception("Error: impossible to read records from {$filepath}.csv");
        }
    }

    public function addRecordToDB(array $record)
    {
        $user = User::firstOrNew(['email' => $record['email']], $record);

        if ($user->exists)
            throw new Exception("Warning: The record {$record['name']}, {$record['surname']}, {$record['email']} is duplicated, so it will not be added to the database\n");

        if (!$this->dryRun)
            $user->save();
    }
}
