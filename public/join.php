<?php
include('../src/lib/templating.php');
include('header.php');
include('../src/include.php');

$errors = [];

$gameid = preg_replace('/\D/', '', trim(@$_REQUEST['gameid'])); // replace non digit by nothing
$playername = trim(@$_REQUEST['playername']);

$Game = new DAOGame();
$Player = new DAOPlayer();
$action = @$_REQUEST['action'];
if ($action == 'post')
{
  if ($gameid == '')
    $errors[] = T('!badgameid');
  else
  {
    

    // Check if the game exists and use the initial amount of money to give to the newly created user
    $thegames = $Game->loadGameFromDB($gameid); 
    if (count($thegames) === 0)
      $errors[] = T('!badgameid');
    else
    {
      $game = $thegames[0];
      $playerid = $Player->createPlayer($playername, $gameid, 0);
      if ($playerid !== false)
      {
        
        $Game->giveBankMoneyToPlayer($gameid, $playerid, $game['bank_init']);
        header("Location: ".$CONFIG['APP']['BASE_URL']."play.php?gameid=$gameid&playerid=$playerid");
        exit;
      }
    }
  }
  if ($playername == '')
    $errors[] = T('!badplayername');
}

?>
<h1><?=T('join_title')?></h1>
<form method="post" action="join.php?action=post">

<?php
foreach($errors as $e)
{
  ?>
  <div class="error"><?=$e?></div><br/>
  <?php
}
?>

<?=T('join_game_id')?> : <input name="gameid" type="text" value="<?=$gameid?>" pattern="[\- 0-9]*"/><br/>
<?=T('join_player_name')?> : <input name="playername" type="text" value="<?=$playername?>"/><br/>

<input class="button button--secondary button--medium button--solid" name="start" type="submit" value="<?=T('join_start', true)?>"/>

</form>



<hr/>
<a href="index.php"><?=T('go_to_welcome')?></a>