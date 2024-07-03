<?php
/**
 * Author: Zhaoguo Han,Feng Qi, Shanghao Li
 * Date: Nov 26 2023
 * Version: 1.0
 * Description: This common file that establishes a database connection using credentials 
 *              from db_credentials.php. It  contains functions for common database operations.
 *
 */
require_once 'db_credentials.php';

function db_connect()
{
    $connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    confirm_db_connect();
    return $connection;
}

function db_disconnect($connection)
{
    if (isset($connection)) {
        mysqli_close($connection);
    }
}

function confirm_db_connect()
{
    if (mysqli_connect_errno()) {
        $msg = "Database connection failed: ";
        $msg .= mysqli_connect_error();
        $msg .= " (" . mysqli_connect_errno() . ")";
        exit($msg);
    }
}

function confirm_result_set($result_set)
{
    if (!$result_set) {
        exit("Database query failed.");
    }
}
