<?php

declare(strict_types=1);

namespace App\Services;

use League\Csv\Reader;
use App\Services\UserRepository;
use Exception;
use Iterator;

use function App\normalization;
use function App\is_email_valid;


class UserUpload
{
    protected bool $dryRun;
    protected UserRepository $userRepository;


    public function __construct(bool $dryRun, UserRepository $userRepository)
    {
        $this->dryRun           = $dryRun;
        $this->userRepository   = $userRepository;

        if (!$this->dryRun)
            $userRepository->addUsersTable();
    }


    public function run(string $filepath): void
    {
        $records = $this->getRecordsFromCSV($filepath);

        foreach ($records as $record) {
            try {
                $record = normalization($record);

                if (!is_email_valid($record['email']))
                    throw new Exception("Warning: email {$record['email']} is not valid and will not be added to the database \n");

                $user = $this->userRepository->addRecordToDB($record);

                if (!$this->dryRun)
                    $user->save();
            } catch (\Throwable $e) {
                echo $e->getMessage();
                continue;
            }
        }
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
}
