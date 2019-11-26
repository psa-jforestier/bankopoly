<?php

// Configuration file
global $CONFIG;
// Database MySQL
$CONFIG['DB_DSN'] = 'mysql:dbname=testdb;host=localhost';
// Database SQLITE
$CONFIG['DB_DSN'] = 'sqlite:'.realpath(dirname(__FILE__).'/../var/bankopoloy.sqlite');
$CONFIG['DB_USER'] = 'username';
$CONFIG['DB_PASS'] = 'password';

$CONFIG['APP']['BASE_URL'] = 'http://'.@$_SERVER["HTTP_HOST"].'/';
$CONFIG['GAME']['BANK_MIN']  =         10;
$CONFIG['GAME']['BANK_MAX']  = 1000000000;
$CONFIG['GAME']['BANK_INIT'] =    1000000;
$CONFIG['GAME']['PLAYER_INIT'] =     1500;
$CONFIG['GAME']['ID_LENGTH'] = 8;
$CONFIG['GAME']['RELOAD_MIN'] = 3;
$CONFIG['GAME']['RELOAD_MAX'] = 7;

$CONFIG['PURGE'] = 24*60*60; // delete old game

// Load extra params if exists
if (file_exists(dirname(__FILE__).'/parameters.extra.php'))
  include_once(dirname(__FILE__).'/parameters.extra.php');

