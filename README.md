# Catalyst PHP Programming Evaluation

## Requirements
- PHP 8.1.x
- MySQL database server >= 5.7 or MariaDB 10.x
- Create an empty database

## Installation
- Clone this repo
- Ensure you have Composer installed on your system. You can download and install it from the official website: https://getcomposer.org/download/
- Run `composer install`
- Set driver and database name in the config if needed, default values are:
  - `driver   => 'mysql'`
  - `database => 'catalyst'`

## Dependencies
This project use the following dependencies:
- [illuminate/database](https://packagist.org/packages/illuminate/database)
- [league/csv](https://csv.thephpleague.com/)

## How to use it

`--file [csv file name]    Name of the CSV to be parsed`<br>
`--create_table            Build the MySQL users table (no further action)`<br>
`--dry_run                 Run the script without altering the database`<br>
`-u                        MySQL username`<br>
`-p                        MySQL password`<br>
`-h                        MySQL host`<br>
`--help                    Display this help message`<br>

To upload users data to the database, run:<br>
`php user_upload.php --file [filename] -u [db username] -p [db password] -h [db host]`

To run the script without applying changes:<br>
`php user_upload.php --file [filename] -u [db username] -p [db password] -h [db host] --dry_run`

To create the users table:<br>
`php user_upload.php --file [filename] -u [db username] -p [db password] -h [db host] --create_table`