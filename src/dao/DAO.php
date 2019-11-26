<?php
class DAO
{
  var $pdo;
  public function __construct()
  {
    $this->pdo = Database::getInstance()->pdo;
  }
  

}