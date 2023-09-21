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
