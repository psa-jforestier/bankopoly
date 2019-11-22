<?php

// Configuration file
global $CONFIG;
// Database MySQL
$CONFIG['DB_DSN'] = 'mysql:dbname=testdb;host=127.0.0.1:3306';
// Database SQLITE
$CONFIG['DB_DSN'] = 'sqlite:'.realpath(dirname(__FILE__).'/../var/bankopoloy.sqlite');
$CONFIG['DB_USER'] = 'username';
$CONFIG['DB_PASS'] = 'password';

$CONFIG['APP']['BASE_URL'] = '/';
$CONFIG['GAME']['BANK_MIN']  =         10;
$CONFIG['GAME']['BANK_MAX']  = 1000000000;
$CONFIG['GAME']['BANK_INIT'] =    1000000;
$CONFIG['GAME']['PLAYER_INIT'] =     1500;
$CONFIG['GAME']['ID_LENGTH'] = 8;

$CONFIG['PURGE'] = 24*60*60; // delete old game

