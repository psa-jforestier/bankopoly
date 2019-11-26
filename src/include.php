<?php
$here = dirname(__FILE__);
include_once("$here/../config/parameters.php");
include_once("$here/lib/Database.php");
include_once("$here/lib/StringUtils.php");
include_once("$here/lib/bankopoly.php");
include_once("$here/dao/DAO.php");
include_once("$here/dao/Game.php");
include_once("$here/dao/Player.php");
include_once("$here/dao/Operation.php");

// Init db access
Database::initInstance($CONFIG['DB_DSN'], $CONFIG['DB_USER'], $CONFIG['DB_PASS']);