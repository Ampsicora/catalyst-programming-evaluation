<?php

declare(strict_types=1);


function displayHelp()
{
    echo "Usage: php upload_users.php [OPTIONS]\n";
    echo "Options:\n";
    echo "  --file [csv file name]    Name of the CSV to be parsed\n";
    echo "  --create_table            Build the MySQL users table (no further action)\n";
    echo "  --dry_run                 Run the script without altering the database\n";
    echo "  -u                        MySQL username\n";
    echo "  -p                        MySQL password\n";
    echo "  -h                        MySQL host\n";
    echo "  --help                    Display this help message\n";
}

function optionsCheck(array $options)
{
    // Check for --help option
    if (isset($options['help'])) {
        displayHelp();
        exit(0);
    }

    // Check for required options
    if (!isset($options['file'], $options['u'], $options['p'], $options['h']))
        throw new Exception("Error: Missing required options. Use --help for usage information.\n", 1);
}

function is_email_valid(string $email): bool
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function normalization(array $record): array
{
    $record['name']     = ucfirst(strtolower(trim($record['name'])));

    $record['surname']  = ucfirst(strtolower(trim($record['surname'])));

    $record['email']    = filter_var($record['email'], FILTER_SANITIZE_EMAIL);;
    $record['email']    = strtolower(trim($record['email']));

    return $record;
}
