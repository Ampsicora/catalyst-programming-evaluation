<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\User;
use Exception;


class UserRepository
{
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

    public function addRecordToDB(array $record)
    {
        $user = User::firstOrNew(['email' => $record['email']], $record);

        if ($user->exists)
            throw new Exception("Warning: The record {$record['name']}, {$record['surname']}, {$record['email']} is duplicated, so it will not be added to the database\n");

        return $user;
    }
}
