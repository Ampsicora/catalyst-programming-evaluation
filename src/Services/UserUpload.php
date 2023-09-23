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
        $emails     = [];
        $chunk      = [];

        foreach ($records as $record) {
            try {
                $record = normalization($record);

                if (!is_email_valid($record['email'])) {
                    $this->userRepository->incrementRecordCounter('invalid');
                    throw new Exception("Warning: email {$record['email']} is not valid and will not be added to the database \n");
                }

                $emails[] = $record['email'];
                $chunk[] = $record;

                if (!$this->dryRun && count($chunk) === $this->chunkSize) {
                    $this->userRepository->countEmailDuplications($emails);
                    $this->userRepository->addChunkToDB($chunk);
                    $chunk = [];
                }
            } catch (\Throwable $e) {
                echo $e->getMessage();
                continue;
            }
        }

        if (!empty($chunk) && !$this->dryRun) {
            $this->userRepository->countEmailDuplications($emails);
            $this->userRepository->addChunkToDB($chunk);
            $chunk = [];
        }

        $counters = $this->userRepository->getRecordCounter();
        echo "New: {$counters['new']} | Duplications: {$counters['duplications']} | Invalid: {$counters['invalid']}";
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
