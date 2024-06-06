<?php

namespace db;

use mysqli;

class DBConnection
{
    private static string $dbUsername = "pawsomeadmin";
    private static string $dbPassword = "paw1234";
    private static string $dbHost = "localhost";
    private static string $dbName = "pawsomedb";

    public static function getConnection(): mysqli
    {
        return new mysqli(
            DBConnection::$dbHost,
            DBConnection::$dbUsername,
            DBConnection::$dbPassword,
            DBConnection::$dbName
        );
    }
}