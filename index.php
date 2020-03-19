<?php

$host = 'localhost';
$username = 'root';
$password = 'root';
$charset = 'utf8';
$collation = 'utf8_unicode_ci';

$pdo = new PDO("mysql:host=$host;charset=$charset", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES '$charset' COLLATE '$collation'");
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

function generateRandomString($length = 10, $prefix = "", $upper = true, $lower = true, $number = true, $symbol = true)
{
    $low = "abcdefghijklmnopqrstuvyxwz";
    $num = "0123456789";
    $sym = "!@$%&*()_+";
    $upp = "ABCDEFGHIJKLMNOPQRSTUVYXWZ";

    $pwd = "";

    if ($upper) $pwd .= str_shuffle($upp);
    if ($lower) $pwd .= str_shuffle($low);
    if ($number) $pwd .= str_shuffle($num);
    if ($symbol) $pwd .= str_shuffle($sym);

    return $prefix . substr(str_shuffle($pwd), 0, $length);
}

$databaseName = generateRandomString(10, "db_", false, true, false, false);
$userName = generateRandomString(10, "user_", false, true, false, false);
$pwd = generateRandomString(20);

$ddlCreateDatabase = "CREATE DATABASE $databaseName CHARACTER SET $charset COLLATE $collation";
$ddlCreateUserLocalhost = "CREATE USER $userName@'localhost' IDENTIFIED BY '$pwd'";
$ddlCreateUserExternal = "CREATE USER $userName@'%' IDENTIFIED BY '$pwd'";
$ddlGrantAccessLocalhost = "GRANT ALL ON $databaseName.* TO $userName@'localhost'";
$ddlGrantAccessExternal = "GRANT ALL ON $databaseName.* TO $userName@'%'";

$pdoResult[] = $pdo->exec($ddlCreateDatabase);
$pdoResult[] = $pdo->exec($ddlCreateUserLocalhost);
$pdoResult[] = $pdo->exec($ddlCreateUserExternal);
$pdoResult[] = $pdo->exec($ddlGrantAccessLocalhost);
$pdoResult[] = $pdo->exec($ddlGrantAccessExternal);
$pdoResult[] = $pdo->exec("FLUSH PRIVILEGES");

$createdDatabase = $pdo->query("SHOW DATABASES LIKE '$databaseName'")->fetchAll();

if (count($createdDatabase)) {
    echo "DB_DATABASE=$databaseName\n";
    echo "DB_USERNAME=$userName\n";
    echo "DB_PASSWORD=\"$pwd\"\n\n";
}

var_dump($createdDatabase);

$pdo = null;
