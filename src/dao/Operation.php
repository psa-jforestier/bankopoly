<?php
class DAOHistory extends DAO
{
  public function getOperations($game_id, $player_id)
  {
    $stmt = $this->pdo->prepare("select * from `operation` where game_id = :game_id and (from_player_id=:player_id or to_player_id=:player_id) order by date_op desc");
    $stmt->execute([
      ':game_id'=>$game_id,
      ':player_id'=>$player_id
    ]);
    $ops = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) 
    {
      $ops[] = $row;
    }
    return $ops;
  }
}
