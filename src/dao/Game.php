<?php
class DAOGame extends DAO
{
  /*
  var $pdo;
  public function __construct()
  {
    $this->pdo = Database::getInstance()->pdo;
  }*/
  
  private function createNewId()
  {
    // Generate a random game id;
    // Number of minute since the begining of the day in UTC
    $m = (date('H') * 60) + date('m');
    // maximum is 24*60 = 1440
    $m = 100 * ($m / 1440); //
    $m = $m % 100; 
    // a random number
    $r = sprintf("%05d", rand(0, 99999));
    // Last digit of the code is based on the seconds
    $s = date('s') % 10;
    //return "88381101";
    return "$m$r$s";
  }
  public function createNewGame()
  {
    
    $nbtry = 3;
    while($nbtry > 0)
    {
      $game_id = $this->createNewId();
      // Try to insert the gameid
      $q = "insert into game(game_id, date_begin) values(:game_id, :date_begin)";
      $stmt = $this->pdo->prepare($q);

      $stmt->bindValue(':game_id', $game_id);
      $stmt->bindValue(':date_begin', Database::NOW());
      $r = $stmt->execute();
      if ($r === false)
      { // Cant insert, try a new id
        $nbtry--;
        $game_id = -1;
      }
      else
      {
        
        break;
      }
    }
    return $game_id;
    
  }
  
  public function getNumberOfGames()
  {
    $stmt = $this->pdo->prepare("select count(*) as nb from game");
    $stmt->execute();
    $games = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) 
    {
      $games[] = $row;
    }
    return $games[0];
  }
  
  public function loadGameFromDB($game_id)
  {
    
    $stmt = $this->pdo->prepare("select * from game where game_id=:game_id limit 1");
    $stmt->execute([':game_id'=>$game_id]);
    $games = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) 
    {
      $games[] = $row;
    }
    return $games;
  }
  
  public function updateGame($gameid, $playerid, $bankinit, $playerinit)
  {
    $stmt = $this->pdo->prepare("update game set bank_init = :playerinit, bank_start = :bankinit, bank_current = :bankinit, bank_player_id = :playerid where game_id=:gameid");
    $r = $stmt->execute([
      ':bankinit'=>$bankinit,
      ':playerinit'=>$playerinit,
      ':playerid'=>$playerid,
      ':gameid'=>$gameid
    ]);
    return $r;
  }
  
  /**
   ** The bank give money to a player.
   ** Update the game gameid by debiting $amount of money, and give the money to $playerid
   **/
  public function giveBankMoneyToPlayer($game_id, $player_id, $amount)
  {
    // Get the game
    $g = $this->loadGameFromDB($game_id);
    if (count($g) === 0)
      return -1;
    $game = $g[0];
    $stmt = $this->pdo->prepare("update game set bank_current = bank_current - :amount where game_id = :game_id");
    $r = $stmt->execute([
      ':amount'=>$amount,
      ':game_id'=>$game_id
    ]);
    $stmt = $this->pdo->prepare("update player set current = current + :amount where id = :player_id");
    $r = $stmt->execute([
      ':amount'=>$amount,
      ':player_id'=>$player_id
    ]);
    //insert into game(game_id, date_begin) values(:game_id, :date_begin)
    $stmt = $this->pdo->prepare("
      insert into operation(date_op, game_id, from_player_id, to_player_id, amount) 
      values(:date_op, :game_id, 0, :to_player_id, :amount)
      ");
    $r = $stmt->execute([
      ':date_op'=>Database::NOW(),
      ':game_id'=>$game_id,
      ':to_player_id'=>$player_id,
      ':amount'=>$amount
    ]);
  }
  
  /**
   ** Give money from a player (or 0 for the bank) to an other player (or 0 for the bank)
   **/
  public function giveMoney($game_id, $from_id, $to_id, $amount)
  {
    // Get the game
    $g = $this->loadGameFromDB($game_id);
    if (count($g) === 0)
      return -1;
    $game = $g[0];
    
    // Decrease from account
    if ($from_id == 0)
    {
      // From the bank
      $stmt = $this->pdo->prepare("update game set bank_current = bank_current - :amount where game_id = :game_id");
      $r = $stmt->execute([
        ':amount'=>$amount,
        ':game_id'=>$game_id
      ]);
    }
    else
    {
      // From a player
      $stmt = $this->pdo->prepare("update player set current = current - :amount where id = :from_id");
      $r = $stmt->execute([
        ':amount'=>$amount,
        ':from_id'=>$from_id
      ]);
    }
    // Increase to account
    if ($to_id == 0)
    {
      // To the bank
      $stmt = $this->pdo->prepare("update game set bank_current = bank_current + :amount where game_id = :game_id");
      $r = $stmt->execute([
        ':amount'=>$amount,
        ':game_id'=>$game_id
      ]);
    }
    else
    {
      // From a player
      $stmt = $this->pdo->prepare("update player set current = current + :amount where id = :to_id");
      $r = $stmt->execute([
        ':amount'=>$amount,
        ':to_id'=>$to_id
      ]);
    }
    // Record history
    $stmt = $this->pdo->prepare("
      insert into operation(date_op, game_id, from_player_id, to_player_id, amount) 
      values(:date_op, :game_id, :from_id, :to_id, :amount)
      ");
    $r = $stmt->execute([
      ':date_op'=>Database::NOW(),
      ':game_id'=>$game_id,
      ':from_id'=>$from_id,
      ':to_id'=>$to_id,
      ':amount'=>$amount
    ]);
  }
  
  public function purgeOldGame($seconds)
  {
    // Find all old game
    $now = time();
    $date = date(DATE_RFC3339, $now - $seconds);
    /**
    $stmt = $this->pdo->prepare("select game_id from game where date_begin < :date");
    $r = $stmt->execute([
      ':date'=>$date
    ]);
    $games = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) 
    {
      $games[] = $row;
    }
    var_dump($games);
    **/
    $return = array();
    // Remove old games
    $stmt = $this->pdo->prepare("delete from game where date_begin < :date");
    $r = $stmt->execute([
      ':date'=>$date
    ]);
    $return['deletedgames'] = $stmt->rowCount();
    // Remove orphan players
    $stmt = $this->pdo->prepare("delete from player where id in (select 
p.id
from player p
left join game g on p.game_id = g.game_id
where g.game_id is NULL)");
    $r = $stmt->execute();
    $return['deletedplayers'] = $stmt->rowCount();
    // Remove orphan operation
        $stmt = $this->pdo->prepare("delete from operation where id in (select 
o.id
from operation o
left join game g on o.game_id = g.game_id
where g.game_id is NULL)");
    $r = $stmt->execute();
    $return['deletedoperations'] = $stmt->rowCount();
    return $return;
  }
}