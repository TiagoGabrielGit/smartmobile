<?php

//Credenciais de acesso ao BD
define('HOST', '177.126.153.21');
define('USER', 'dbsistem');
define('PASS', 'gtIBJNK094357*&2@');
define('DBNAME', 'dbsistem');

$pdo = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';', USER, PASS);

