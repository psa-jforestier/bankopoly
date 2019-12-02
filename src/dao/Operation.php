<?php
class DAOHistory extends DAO
{
  public function getOperations($game_id, $player_id)
  {
    $stmt = $this->pdo->prepare("
    select 
      id, date_op, game_id, from_player_id, to_player_id, amount
    from `operation` where game_id = :game_id and (from_player_id=:player_id or to_player_id=:player_id) order by date_op desc");
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
  
  public function getLastOperationDate($game_id)
  {
    $stmt = $this->pdo->prepare("
    select
      date_op
    from operation
      where game_id = :game_id
      order by date_op desc
      limit 1");
    $stmt->execute([
      ':game_id'=>$game_id
    ]);
    $dates = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) 
    {
      $dates[] = $row;
    }
    if (count($dates) == 0)
      return 0;
    else
      return $dates[0]['date_op'];
  }
}
