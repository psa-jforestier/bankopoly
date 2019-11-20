<?php
include('../src/lib/templating.php');
include('header.php');
include('../src/include.php');

$errors = [];

$gameid = @$_REQUEST['gameid'];
$bankinit = trim(@$_REQUEST['bankinit']);
$playerinit = trim(@$_REQUEST['playerinit']);
$playername = trim(@$_REQUEST['playername']);

$Game = new DAOGame();

// Form posted
if (@$_REQUEST['action'] == 'post')
{
  if ($bankinit < $CONFIG['GAME']['BANK_MIN'] || $bankinit > $CONFIG['GAME']['BANK_MAX'] || !is_numeric($bankinit) )
    $errors[] = T('!badbankamount');
  
  if ($playerinit > $bankinit || !is_numeric($playerinit))
    $errors[] = T('!badplayeramount');
  
  if (count($Game->loadGameFromDB($gameid)) === 0)
  {
    $errors[] = T('!badgameid');
  }
  else
  {
    
    // Game is ready to be created 
    if ($playername != '')
    {
      $Player = new DAOPlayer();
      $pid = $Player->createPlayer($playername, $gameid, 0);
      $Game->updateGame($gameid, $pid, $bankinit, $playerinit);
      
    }
  }
}




// 1st load of the page
if ($gameid == '')
{
  $gameid = $Game->createNewGame();
  $bankinit = $CONFIG['GAME']['BANK_INIT'];
  $playerinit = $CONFIG['GAME']['PLAYER_INIT'];
  if ($gameid == -1)
  {
    $gameid = '?';
    $errors[]= T('!generateid');
  }
}
else
{

}

function formatGameId($id)
{
  if (!is_numeric($id))
    return $id;
  return number_format($id, 0, ',', '-');
}

?>

<form method="post" action="new.php?action=post">
<h1><?=T('new_bank_title')?></h1>
<?php
foreach($errors as $e)
{
  ?>
  <div class="error"><?=$e?></div><br/>
  <?php
}
?>

<?=T('new_bank_amount') ?> : <input name="bankinit" type="text" required value="<?=$bankinit?>" min="<?=$CONFIG['GAME']['BANK_MIN']?>" max="<?=$CONFIG['GAME']['BANK_MAX']?>" placeholder=""/><br/>

<?=T('new_start_amount') ?> : <input name="playerinit" type="text" required value="<?=$playerinit?>" min="<?=$CONFIG['GAME']['BANK_MIN']?>" max="<?=$CONFIG['GAME']['BANK_MAX']?>" placeholder=""/><br/>

<?=T('new_game_id') ?> : <br/>
<div class="panel"><code>[ <?= formatGameId($gameid) ?> ]</code></div><br/>
<input type="hidden" name="gameid" value="<?=$gameid?>"/>

<?=T('new_bank_player_name') ?> : <input name="playername" type="text" value="<?=$playername?>" /><br/>
<hr/>
<input class="button button--secondary button--medium button--solid" name="start" type="submit" value="<?=T('new_start', true)?>"/>
<hr/>
<a href="index.php"><?=T('go_to_welcome')?></a>