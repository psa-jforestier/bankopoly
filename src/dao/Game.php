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
}