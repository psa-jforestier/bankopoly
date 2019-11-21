<?php
include('../src/lib/templating.php');
include('header.php');
include('../src/include.php');

$errors = [];
$my_name = '?';

$gameid = trim(@$_REQUEST['gameid']);
$playerid = trim(@$_REQUEST['playerid']);
$to_playerid = trim(@$_REQUEST['to_playerid']);
$amount = trim(@$_REQUEST['amount']);
$amountplayer = trim(@$_REQUEST['amountplayer']);

$Game = new DAOGame();
$Player = new DAOPlayer();
$History = new DAOHistory();


if ($amount != '') // Bank form posted
{
  $amount = 0 + $amount;
  if ($amount <= 0)
    $errors[] = T('!badamount');
  else
  {

    $Game->giveBankMoneyToPlayer($gameid, $to_playerid, $amount);
    
  }
}

if ($amountplayer != '') // Player form posted
{
  $amountplayer = 0 + $amountplayer;
  if ($amountplayer <= 0)
    $errors[] = T('!badamount');
  else
  {
    if ($playerid == $to_playerid)
      $errors[] = T('!badself');
    else
      $Game->giveMoney($gameid, $playerid, $to_playerid, $amountplayer);
    
  }
}

$thegames = $Game->loadGameFromDB($gameid); 
if (count($thegames) === 0)
  $errors[] = T('!badgameid');
$game = $thegames[0];

$players = $Player->getPlayersOfAGame($gameid);
$is_bankmanager = ($game['bank_player_id'] == $playerid);
foreach($players as $p)
{
  if ($p['id'] == $playerid)
  {
    $my_name = $p['name'];
    $my_account = $p;
    break;
  }
}

if ($my_account['current'] <= 0)
  $errors[] = T('!bankroute');

$history = $History->getOperations($gameid, $playerid);


?>

<h1><?=T('play_game_title')?></h1>
<div class="playerinfo"><?=$my_name?> - <span class="currency"><?=formatAmount($my_account['current'])?></span></div>
<div class="panel"><code>[ <?= formatGameId($gameid) ?> ]</code></div><br/>
<table border="1">
<tr>
<?php
foreach($players as $p)
{
  ?>
  <td><?=$p['name']?></td>
  <?php
}
?>
</tr>
<tr>
<?php
foreach($players as $p)
{
  ?>
  <td align="right" class="currency"><?=formatAmount($p['current'])?></td>
  <?php
}
?>
</tr>
</table>

<?php
foreach($errors as $e)
{
  ?>
  <div class="error"><?=$e?></div><br/>
  <?php
}
?>

<hr/>
<?php

if($is_bankmanager === true)
{ // am i the bankmanager
  ?>
<h2>&#128181;<?=T('play_bank_title')?> <a href="#" onclick='$("#bankmanager").toggle(); return false;'>...</a></h2>
<div id="bankmanager">
<form action="play.php?gameid=<?=$gameid?>&playerid=<?=$playerid?>" method="post">
<span id="bankamount" class="currency"><?=formatAmount($game['bank_current']) ?></span>
<br/>
<?=T('play_bank_give')?> <input type="numeric" id="amount" name="amount" size=8 value="<?=$amount?>"/>
<?=T('play_bank_to')?>
<select name="to_playerid">
  <!-- <option value="0"><?=T('play_bank_title')?></option> -->
  <?php
  foreach($players as $p)
  {
    echo "<option value=\"", $p['id'], "\"";
    if ($p['id'] == $to_playerid) echo " selected ";
    echo ">";
    echo $p['name'];
    echo "</option>\n";
  }
  ?>
</select>
<input class="button button--secondary button--small button--solid" name="start" type="submit" value="<?=T('play_bank_ok')?>"/>
<br/>
<a href="#" onclick='$("#amount").val($(this).text()); return false;'>50</a> | <a href="#" onclick='$("#amount").val($(this).text()); return false;'>200</a>
</div>
</form>
<?php
} // am i the bankmanager
?>

<h2><?=T('play_player')?></h2>
<form action="play.php?gameid=<?=$gameid?>&playerid=<?=$playerid?>" method="post">
<div class="playeraction">
  <?=T('play_pay')?> <input type="numeric" id="amountplayer" name="amountplayer" value="<?=$amount?>" size=8/> <?=T('play_pay_to')?> 
  <select name="to_playerid">
  <option value="0">&#128181;<?=T('play_bank_title')?></option>
  <?php
  foreach($players as $p)
  {
    if ($p['id'] != $playerid)
    {
      echo "<option value=\"", $p['id'], "\"";
      if ($p['id'] == $to_playerid) echo " selected ";
      echo ">";
      echo $p['name'];
      echo "</option>\n";
    }
  }
  ?>
</select>
<input class="button button--secondary button--small button--solid" name="start" type="submit" value="<?=T('play_bank_ok')?>"/>
<br/>
<a href="#" onclick='$("#amountplayer").val($(this).text()); return false;'>50</a> | <a href="#" onclick='$("#amountplayer").val($(this).text()); return false;'>100</a> | <a href="#" onclick='$("#amountplayer").val($(this).text()); return false;'>150</a> | <a href="#" onclick='$("#amountplayer").val($(this).text()); return false;'>200</a>

</form>
</div>

<h2><?=T('play_histo')?></h2>
<table border="1" class="history">
  <tr><td colspan=3><?=T('histo_account')?> : <span class="currency"><?=formatAmount($my_account['current'])?></span></td></tr>
  <tr>
    <th><?=T('histo_col1')?></th>
    <th><?=T('histo_col2')?></th>
    <th><?=T('histo_col3')?></th>
  </tr>
<?php
  foreach($history as $h)
  {
    $dt = substr($h['date_op'], 11, 8);
    $from = $h['from_player_id'];
    $to = $h['to_player_id'];
    $sign = '';
    $name = '?';
    
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
    ?>
  <tr>
    <td><?=$dt?></td>
    <td align=right><span id="bankamount" class="currency"><?=$sign?><?=formatAmount($h['amount']) ?></span></td>
    <td><?=$name?></td>
  </tr>
    <?php
  }
?>
</table>
<hr/>
<a href="index.php"><?=T('go_to_welcome')?></a>