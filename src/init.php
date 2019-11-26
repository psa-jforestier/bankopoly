<?php

// This script must be run on cmd line. It is used to initialize the database (create the schema etc).
// If the DB already exists, the script will fail, or use "--recreate" to destory and recreate the DB

if (!file_exists('../config/parameters.php'))
  die('Please create a config/parameters.php file !');

include_once('include.php');
global $CONFIG;

if (StringUtils::startsWith($CONFIG['DB_DSN'], 'sqlite'))
{
  echo "Use SQLITE as database backend : ".$CONFIG['DB_DSN']."\n";
} else if (StringUtils::startsWith($CONFIG['DB_DSN'], 'mysql'))
{
  echo "Use MYSQL as database backend\n";
} else
{
  die('Database DSN '.$CONFIG['DB_DSN'].' is not valid. Only MySQL or SQLite are suported.');
}

// Check if database exists
$dao = new DAO($CONFIG['DB_DSN'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);

try
{
  $dbh = new \PDO($CONFIG['DB_DSN'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);
}
catch (PDOException $e) 
{
  die( 'Fail to connect : ' . $e->getMessage() );
}

$sql = [];

/**
 ** SQL instruction must be compatible with MySQL and SQLite.
 ** Use the special MySQL comment to include keyword only valid for MySQL.
 **/
$IF_MYSQL="/*!40101";
$ENDIF_MYSQL = "*/";

$sql = [];
if (@$argv[1] == '--recreate')
{
  $sql[] = "drop table if exists game; drop table if exists  player; drop table if exists operation;";
}
$sql[] = "
create table `game` (
  game_id char(8) primary key,
  bank_start decimal(10,0),
  bank_current decimal(10,0),
  bank_init decimal(10,0),
  date_begin datetime,
  bank_player_id integer
  )
";
$sql[] = "create unique index x_game_game_id on game(game_id)";
$sql[] = "
create table `player` (
  id integer primary key $IF_MYSQL AUTO_INCREMENT $ENDIF_MYSQL,
  game_id char(8),
  name varchar(255),
  current decimal(10,0),
  date_begin datetime
 )
";
$sql[] = "create index x_player_game_id on player(game_id)";
$sql[] = "
create table `operation` (
  id integer primary key $IF_MYSQL AUTO_INCREMENT $ENDIF_MYSQL,
  date_op datetime,
  game_id char(8),
  from_player_id integer,
  to_player_id integer,
  amount decimal(10,0)
 )
";
$sql[] = "create index x_op_game_id on operation(game_id)";
$sql[] = "create index x_op_from_player_id on operation(from_player_id)";
$sql[] = "create index x_op_to_player_id on operation(to_player_id)";

foreach ($sql as $i=>$s) 
{
  echo "Execution sql $i\n";
  $res = $dbh->exec($s);
  if ($res === false)
  {
    echo "Error when execution SQL :\n";
    echo $s;
    $err = $dbh->errorInfo();
    var_dump($err);
    if ($err[0] == "HY000")
      echo "Try --recreate to drop and recreate tables";
    die;
  } 
}
