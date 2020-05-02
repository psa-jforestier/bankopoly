<?php

$T = microtime(true);
include('../src/lib/templating.php');
include('../src/include.php');

$action = @$_REQUEST['action'];
$gameid = @$_REQUEST['gameid'];
$playerid = @$_REQUEST['playerid'];



if ($action == 'purge')
{
  $Game = new DAOGame();
  $result = $Game->purgeOldGame($CONFIG['PURGE'], $CONFIG['PURGE_KEEP_OLD_GAME']);
  $result['time_s'] = (microtime(true) - $T);
  if ($result['deletedgames'] != 0)
  {
    $LOGGER->info(sprintf('Old objects purged : %d game, %d player, %d operations', 
    $result['deletedgames'], $result['deletedplayers'], $result['deletedoperations']));
  }
  header('content-type: application/json');
  echo json_encode($result);
  
  exit;
}
else if ($action == 'info')
{
  
  $History = new DAOHistory();
  $history = $History->getOperations($gameid, $playerid);
  $lastdate = $History->getLastOperationDate($gameid);
  //var_dump($history);
  $Player = new DAOPlayer();
  $players = $Player->getPlayersOfAGame($gameid);
  $my_account = $Player->getPlayer($playerid, $gameid);
  $Game = new DAOGame();
  $thegames = $Game->loadGameFromDB($gameid); 
  if (count($thegames) === 0)
    $bankamount = 0;
  else
    $bankamount = $thegames[0]['bank_current'];
  $result['reload'] = rand(1000*$CONFIG['GAME']['RELOAD_MIN'], 1000*$CONFIG['GAME']['RELOAD_MAX']);
  $result['last_action_date'] = $lastdate;
  $result['bankamount']= formatAmount($bankamount);
  $result['me'] = $my_account;
  
  $players_html = '<table border="1"><tr>';
  foreach($players as $p)
  {
    $players_html .= '<td>'.$p['name'].'</td>';
  }
  $players_html .= '</tr><tr>';
  foreach($players as $p)
  {
    $players_html .= '<td align="right" class="currency">'.formatAmount($p['current']).'</td>';
  }
  $players_html .= '</tr></table>';
  $result['players_html'] = $players_html;
  $result['players'] = $players;
  $histo = array();
  $histo_content = '';
  foreach($history as $i=>$h)
  {
    $from = $h['from_player_id'];
    $to = $h['to_player_id'];
    if ($to == $playerid)
    {
      $sign = '+';
      if ($from == 0)
        $name = "&#128181;".$_T['play_bank_name'];
      else
        $name = $players[$from]['name'];
    }
    else
    {
      $sign = '-';
      if ($to == 0)
        $name = "&#128181;".$_T['play_bank_name'];
      else
        $name = $players[$to]['name'];
    }
    $histo[] = array(
      'name'=>$name,
      'date_op'=>$h['date_op'],
      'amount'=>$sign.$h['amount']
    );
    $histo_content .= '
  <tr>
    <td><div id="date'.$i.'"></div><script>$("#date'.$i.'").html(new Date("'.$h['date_op'].'").toLocaleTimeString());</script></td>
    <td align=right><span class="currency">'.$sign.formatAmount($h['amount']).'</span></td>
    <td>'.$name.'</td>
  </tr>
  ';
  }
  
  $result['history'] = $histo;
  $result['history_html'] = '
<table border="1" class="history">
  <tr>
    <th>'.T('histo_col1').'</th>
    <th>'.T('histo_col2').'</th>
    <th>'.T('histo_col3').'</th>
  </tr>  
'.$histo_content.'
</table>
';
  
  header('content-type: application/json');
  echo json_encode($result);
  exit;
}

?>
