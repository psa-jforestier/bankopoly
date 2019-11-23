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
  $result = $Game->purgeOldGame($CONFIG['PURGE']);
  $result['time_s'] = (microtime(true) - $T);
  header('content-type: application/json');
  echo json_encode($result);
  exit;
}
else if ($action == 'info')
{
  $History = new DAOHistory();
  $history = $History->getOperations($gameid, $playerid);
  $Player = new DAOPlayer();
  $players = $Player->getPlayersOfAGame($gameid);
  $my_account = $Player->getPlayer($playerid, $gameid);
  $result['reload'] = rand(1000*$CONFIG['GAME']['RELOAD_MIN'], 1000*$CONFIG['GAME']['RELOAD_MAX']);
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
        $name = "&#128181;".$_T['play_bank_title'];
      else
        $name = $players[$from]['name'];
    }
    else
    {
      $sign = '-';
      if ($to == 0)
        $name = "&#128181;".$_T['play_bank_title'];
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
    <td align=right><span id="bankamount" class="currency">'.$sign.formatAmount($h['amount']).'</span></td>
    <td>'.$name.'</td>
  </tr>
  ';
  }
  
  $result['history'] = $histo;
  $result['history_html'] = '
<table border="1" class="history">
  <tr><td colspan=3>'.T('histo_account').' : <span class="currency">'.formatAmount($my_account['current']).'</span></td></tr>
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
