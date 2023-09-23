<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\User;
use Exception;


class UserRepository
{
    protected array $recordCounter = ['add' => 0, 'duplicated' => 0, 'invalid' => 0];


    public function addUsersTable(): void
    {
        try {
            // The table users is dropped following point 4 of script task: "The users database table will need to be created/rebuilt"
            Capsule::schema()->dropIfExists('users');

            Capsule::schema()->create('users', function ($table) {
                $table->increments('id');
                $table->string('email')->unique();
                $table->string('name');
                $table->string('surname');
                $table->timestamps();
            });
        } catch (\Throwable) {
            throw new Exception("Error Creating the user table");
        }
    }

    public function checkRecordDuplication(array $record): void
    {
        $user = User::where('email', '=', $record['email'])->first();

        if (isset($user)) {
            $this->incrementRecordCounter('duplicated');
            throw new Exception("The record {$record['name']}, {$record['surname']}, {$record['email']} is duplicated and will not be added to the db\n");
        } else
            $this->incrementRecordCounter('add');
    }

    public function addRecordToDB(array $record)
    {
        try {
            User::create($record);
        } catch (\Throwable $th) {
            throw new Exception("Error: unable to add record to db");
        }
    }

    public function getRecordCounter(): array
    {
        return $this->recordCounter;
    }

    public function incrementRecordCounter(string $counter): void
    {
        $this->recordCounter[$counter]++;
    }
}
