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
    protected UserRepository $userRepository;
    protected bool $dryRun;
    protected int $chunkSize;


    public function __construct(UserRepository $userRepository, bool $dryRun, int $chunkSize = 500)
    {
        $this->userRepository   = $userRepository;
        $this->dryRun           = $dryRun;
        $this->chunkSize        = $chunkSize;

        if (!$this->dryRun)
            $userRepository->addUsersTable();
    }


    public function run(string $filepath): void
    {
        $records    = $this->getRecordsFromCSV($filepath);
        $chunk      = [];

        foreach ($records as $record) {
            try {
                $record = normalization($record);

                if (!is_email_valid($record['email'])) {
                    $this->userRepository->incrementRecordCounter('invalid');
                    throw new Exception("Warning: email {$record['email']} is not valid and will not be added to the database \n");
                }

                $this->userRepository->checkRecordDuplication($record);

                if (!$this->dryRun)
                    $this->userRepository->addRecordToDB($record);
            } catch (\Throwable $e) {
                echo $e->getMessage();
                continue;
            }
        }

        $counters = $this->userRepository->getRecordCounter();
        echo "Result:\nadded: {$counters['add']}, skipped: {$counters['skip']}, invalid: {$counters['invalid']}";
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
