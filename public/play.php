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

if ($gameid == '')
{
	header('Location: index.php');
	exit;
}
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
<div class="playerinfo"><?=$my_name?> - <span class="currency" id="my_current"><?=formatAmount($my_account['current'])?></span></div>
<div class="panel"><code>[ <?= formatGameId($gameid) ?> ]</code></div><br/>

<div id="players">
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
</div>
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
<fieldset class="bankpanel">
	<legend>&nbsp;&#128181; <?=T('play_bank_title')?> &nbsp; &nbsp;<span style="float: right;"><a class="nostyle" href="#" onclick='$("#bankmanager").toggle(); return false;' >[...]</a>&nbsp;</span></legend>
	<div id="bankmanager">
		<?=T('play_bank_amount')?> : <span id="bankamount" class="currency"><?=formatAmount($game['bank_current']) ?></span><br/>
		<form action="play.php?gameid=<?=$gameid?>&playerid=<?=$playerid?>" method="post">
		<?=T('play_bank_give')?> <input type="number" id="amount" name="amount" size=8 value="<?=$amount?>"/>
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
		<a href="#" class="banknote" onclick='$("#amount").val($(this).text()); return false;'>50</a> | <a href="#" class="banknote" onclick='$("#amount").val($(this).text()); return false;'>200</a>

		</form>
	</div>
</fieldset>
  
<?php
} // am i the bankmanager
?>

<fieldset class="playeraction">
	<legend>&nbsp;<?=T('play_player')?> <?=$my_name?>&nbsp;</legend>
<form action="play.php?gameid=<?=$gameid?>&playerid=<?=$playerid?>" method="post">
  <?=sprintf(T('play_player_amount'), $my_name)?> <span class="currency" id="my_current"><?=formatAmount($my_account['current'])?></span><br/>
  <?=T('play_pay')?> <input type="number" id="amountplayer" name="amountplayer" value="<?=$amount?>" size=1/><span class="currency"></span> <?=T('play_pay_to')?> 
  <select name="to_playerid" id="playerlist">
  <option value="0">&#128181; <?=T('play_bank_name')?></option>
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
<a href="#" class="banknote" onclick='$("#amountplayer").val($(this).text()); return false;'>50</a> | <a href="#" class="banknote" onclick='$("#amountplayer").val($(this).text()); return false;'>100</a> | <a href="#" class="banknote" onclick='$("#amountplayer").val($(this).text()); return false;'>150</a> | <a href="#" class="banknote" onclick='$("#amountplayer").val($(this).text()); return false;'>200</a>

</form>


<input type="hidden" value="gameid=<?=$gameid?>&playerid=<?=$playerid?>" id="params"/>

<br/>

<fieldset class="historypanel">
	<legend>&nbsp;<?=T('play_histo')?>&nbsp;</legend>

<script>
var nextreload = 5000;
(function worker() {
  $.ajax({
    url: 'api.php?action=info&' + $("#params").val() + "&_="+(new Date().getTime()), 
    beforeSend: function(xhr) {
      //$("#my_current").text("?");
    },
    success: function(data) {
      nextreload = data.reload;
      v = (1 * data.me.current);
      v_str = v.toLocaleString();
      if (v_str != $("#my_current").text())
      {
		console.log("current amount of money changed");
        $("#my_current").text(v_str).fadeOut(150).fadeIn(150);
        $("#history").html(data.history_html).fadeOut(150).fadeIn(150);
      }
      nbplayers = Object.keys(data.players).length;
	  nbplayersinlist = $("#playerlist option").length ; // There is always a Bank, and the self player in the list (+1 -1)
	  if (nbplayers != nbplayersinlist)
	  {
		// Refresh player table if change, and the drop down list
		playerselected = $("#playerlist").prop('selectedIndex')
		console.log("current number of players changed. selected = "+playerselected);
		$("#players").html(data.players_html);
		//if (nbplayersinlist < nbplayers)
		{ // TODO find a solution to add/remove player and make it work even if the select is open
			playerlist = $("#playerlist").empty();
			playerlist.append('<option value="0">&#128181;<?=T('play_bank_title')?></option>');
			Object.keys(data.players).forEach(function(item){
				if (item != data.me.id)
				{
					selected = (playerselected == item ? 'selected' : '');
					opt = '<option value="' + item + '" '+selected+'>'+data.players[item].name+"</option>";
					playerlist.append(opt);
				}
			});
		}
		
	  }
      // 
    },
    complete: function() {
      // Schedule the next request when the current one's complete
      setTimeout(worker, nextreload);
    }
  });
})();
/*
(function worker() {
  $.ajax({
    url: 'api.php?action=history&' + $("#params").val(), 
    success: function(data) {
      $('#history').html(data);
      
    },
    complete: function() {
      // Schedule the next request when the current one's complete
      setTimeout(worker, 5000);
    }
  });
})();
*/
//$('#refresh').load('api.php?action=history&' + $("#params").val()+" #history");
/**
setInterval(function(){
      $('#history').load('api.php?action=history&' + $("#params").val());
 },1000);
 **/
</script>


<div id="history">
<table border="1" class="history">
  
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
    ?>
  <tr>
    <td><script>document.write(new Date("<?=$h['date_op']?>").toLocaleTimeString());</script></td>
    <td align=right><span id="bankamount" class="currency"><?=$sign?><?=formatAmount($h['amount']) ?></span></td>
    <td><?=$name?></td>
  </tr>
    <?php
  }
?>
</table>
</div><!-- history -->

</fieldset><!-- history -->
</fieldset><!-- player -->
<hr/>
<a href="index.php"><?=T('go_to_welcome')?></a><br/>
<?php
$join_url = $CONFIG['APP']['BASE_URL'].'join.php?gameid='.$gameid;
?>
<a href="<?=$join_url?>" target="_new"><img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?=urlencode($join_url)?>"/></a>

<hr/>
<fieldset>
	<legend>La banque joue</legend>
	La banque possède : <span class="currency">xxx xxx</span><br/>
	La banque donne [____] à [___\/] [ OK ] <br/>
	[ 50 ] [ 200 ]
</fieldset>
<fieldset>
	<legend>Joueur XX</legend>
	Joueur XX possède : <span class="currency">xxx xxx</span><br/>
	Joueur XX donne [____] à [___\/] [ OK ] <br/>
	[ 50 ] [ 100 ] [ 150 ] [ 200 ] <br/>
	<fieldset>
	<legend>Historique</legend>
	</fieldset>
</fieldset>
<fieldset>
	<legend>Autres joueurs</legend>
	Tableaux des autres joueurs
<fieldset>
<fieldset>
	<legend>Rejoindre la partie \/</legend>
	[ AA-BBB-CCC ]
	[ QR ]