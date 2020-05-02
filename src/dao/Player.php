<?php
class DAOPlayer extends DAO
{

  public function createPlayer($playername, $game_id, $playerinit)
  {
    $stmt = $this->pdo->prepare("insert into player(name, game_id, date_begin, current) values(:name, :game_id, :date_begin, :current)");
    $r = $stmt->execute([
      ':name'=>$playername,
      ':game_id'=>$game_id,
      ':date_begin'=>Database::NOW(),
      ':current'=>$playerinit
    ]);
    if ($r === true)
      return $this->pdo->lastInsertId();
    else
      return false;
  }
  
  public function getPlayersOfAGame($game_id)
  {
    $stmt = $this->pdo->prepare("select * from player where game_id = :game_id order by lower(name)");
    $stmt->execute([':game_id'=>$game_id]);
    $players = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) 
    {
      $players[$row['id']] = $row;
    }
    return $players;
  }
  
  public function getSimplePlayersOfAGame($game_id)
  {
    $stmt = $this->pdo->prepare("select id, name, current from player where game_id = :game_id order by lower(name)");
    $stmt->execute([':game_id'=>$game_id]);
    $players = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) 
    {
      $players[$row['id']] = $row;
    }
    return $players;
  }
  
  public function getPlayer($player_id, $game_id)
  {

    $stmt = $this->pdo->prepare("select * from player where id = :player_id and game_id = :game_id order by lower(name)");
    $stmt->execute([
      ':game_id'=>$game_id,
      ':player_id'=>$player_id
    ]);
    $players = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) 
    {
      $players[] = $row;
    }
    
    return $players[0];
  }
  

}