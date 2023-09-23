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
            throw new Exception("Error creating the user table");
        }
    }

    public function countEmailDuplications(array $emails): int
    {
        return User::whereIn('email', $emails)->count();
    }

    public function addChunkToDB(array $chunk): void
    {
        try {
            User::upsert($chunk, ['email'], []);
        } catch (\Throwable $th) {
            throw new Exception("Error: unable to add records to db:\n{$th}");
        }
    }
}
