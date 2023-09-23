<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\User;
use Exception;


class UserRepository
{
    protected array $recordCounter = ['new' => 0, 'duplications' => 0, 'invalid' => 0];


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

    public function countEmailDuplications(array $emails): void
    {
        $this->recordCounter['duplications'] += User::whereIn('email', $emails)->count();
    }

    public function addChunkToDB(array $chunk)
    {
        try {
            User::upsert($chunk, ['email'], []);
            $this->recordCounter['new'] += count($chunk);
        } catch (\Throwable $th) {
            throw new Exception("Error: unable to add records to db:\n{$th}");
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
