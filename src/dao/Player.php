<?php
class DAOPlayer extends DAO
{

  public function createPlayer($playername, $gameid, $playerinit)
  {
    $stmt = $this->pdo->prepare("insert into player(name, game_id, date_begin, current) values(:name, :game_id, :date_begin, :current)");
    $r = $stmt->execute([
      ':name'=>$playername,
      ':game_id'=>$gameid,
      ':date_begin'=>Database::NOW(),
      ':current'=>$playerinit
    ]);
    if ($r === true)
      return $this->pdo->lastInsertId();
    else
      return false;
  }
}