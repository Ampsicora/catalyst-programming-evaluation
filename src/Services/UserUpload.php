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
    }


    public function run(string $filepath): void
    {
        $records = $this->getRecordsFromCSV($filepath);
        $emails = $chunk = [];
        $total = $duplications = $invalid = 0;

        foreach ($records as $record) {
            $total++;

            try {
                $record = normalization($record);

                if (!is_email_valid($record['email'])) {
                    $invalid++;
                    throw new Exception("Warning: email {$record['email']} is not valid and will not be added to the database \n");
                }

                if (isset($emails[$record['email']])) {
                    $duplications++;
                    continue;
                }

                $emails[$record['email']] = true;
                $chunk[] = $record;

                if (count($chunk) === $this->chunkSize) {
                    $duplications += $this->userRepository->countEmailDuplications(array_keys($emails));
                    !$this->dryRun && $this->userRepository->addChunkToDB($chunk);
                    $chunk = $emails = [];
                }
            } catch (\Throwable $e) {
                echo $e->getMessage();
                continue;
            }
        }

        if (!empty($chunk)) {
            $duplications += $this->userRepository->countEmailDuplications(array_keys($emails));
            !$this->dryRun && $this->userRepository->addChunkToDB($chunk);
            $chunk = $emails = [];
        }

        $inserted = $total - $duplications - $invalid;

        echo "New: {$inserted} | Duplications: {$duplications} | Invalid: {$invalid}";
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
