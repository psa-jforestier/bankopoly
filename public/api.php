<?php
$T = microtime(true);
include('../src/lib/templating.php');
include('../src/include.php');

$action = @$_REQUEST['action'];
header('content-type: application/json');
if ($action == 'purge')
{
  $Game = new DAOGame();
  $result = $Game->purgeOldGame($CONFIG['PURGE']);
  $result['time_s'] = (microtime(true) - $T);
  echo json_encode($result);
  exit;
}
