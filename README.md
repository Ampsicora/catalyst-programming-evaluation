# 📃 Catalyst PHP Programming Evaluation
This CLI software allow you to normalize, validate and upload records from CSV file into the database. <br>
The upload is divided in chunks (default: 500 records) to lighten the requests to db and be able to upload big CSV files.

## ⚠ Requirements
- PHP 8.1.x
- MySQL database server >= 5.7 or MariaDB 10.x
- Create an empty database

## 👩‍💻 Installation
- Clone this repo
- Ensure you have Composer installed on your system. You can download and install it from the official website: https://getcomposer.org/download/
- Go to the root of the project `cd catalyst-programming-evaluation`
- run `composer install`
- Set driver and database name in the '.env.example' if you want to change them. <br>In case you want to change them remember to rename '.env.example' in '.env', default values are:
  - `driver   => 'mysql'`
  - `database => 'catalyst'`

## 👫 Dependencies
This project use the following dependencies:
- [illuminate/database](https://packagist.org/packages/illuminate/database)
- [league/csv](https://csv.thephpleague.com/)
- [vlucas/phpdotenv](https://packagist.org/packages/vlucas/phpdotenv)

## ❓ How to use it

`--file [csv file name]    Name of the CSV to be parsed`<br>
`--create_table            Build the MySQL users table (no further action)`<br>
`--dry_run                 Run the script without altering the database`<br>
`-u [db username]          MySQL username`<br>
`-p [db password]          MySQL password`<br>
`-h [db host]              MySQL host`<br>
`--help                    Display this help message`<br>

To upload users data to the database, run:<br>
`php user_upload.php --file [filename] -u [db username] -p [db password] -h [db host]`

To run the script without applying changes:<br>
`php user_upload.php --file [filename] -u [db username] -p [db password] -h [db host] --dry_run`

To create the users table:<br>
`php user_upload.php --file [filename] -u [db username] -p [db password] -h [db host] --create_table`
