<?php

/* Eigenschaften zur Verbindung zur Datenbank */
$server = 'localhost';
$user = 'root'; 
$pwd = 'root'; 
$db = 'rezept';

/*

Vorteile von PDO:

- Einheitliche Schnittstelle, die es ermöglicht, mit vielen Datenbanken zu arbeiten (z.B. MySQL, PostgreSQL, SQLite, Orace, ...)
  Es kann leichter auf eine andere Datenbank umgestellt werden, ohne den Code grundlegend ändern zu müssen.
- Prepared Statements: SQL Anweisungen werden einmal vorbereitet und dann mehrmals mit verschiedenen Parametern ausgeführt.
  (bietet Sicherheit gegenüber SQL Injection)
- ...

*/

try 
{
  // Erstellung der PDO-Instanz
  $con = new PDO('mysql:host='.$server.';dbname='.$db.';charset=utf8', $user, $pwd);

  // Aktivieren von PDO Exception-Handling:
  $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch (Exception $e)
{
  echo 'Error - Verbindung: '.$e->getCode().': '.$e->getMessage().'<br>';
}