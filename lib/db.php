<?php
// stałe bazy danych i schematów
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_SCHEMA', 'helion');
define('DB_TBL_PREFIX', 'HELION_');

// ustanowienie połączenia z serwerem bazy danych
if (!$GLOBALS['DB'] = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD))
{
    die('Błąd: Nie udało się nawiązać połączenia z bazą danych.');
}
if (!mysql_select_db(DB_SCHEMA, $GLOBALS['DB']))
{
    mysql_close($GLOBALS['DB']);
    die('Błąd: Nie udało się wybrać schematu bazy danych.');
}
