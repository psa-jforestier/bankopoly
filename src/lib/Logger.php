<?php


class Logger {
  var $level;
  const LEVEL_DEBUG = 1;
  const LEVEL_INFO  = 2;
  var $output;
  
  private static $_instance = null;
  
  public static function getInstance($output, $level)
  {
    if(is_null(self::$_instance)) 
    {
      self::$_instance = new Logger($output, $level);
    }
    return self::$_instance;
  }
  
  private function __construct($output, $level)
  {
    $this->output = $output;
    $this->level = $level;
  }
  
  private function write($level, $str)
  {
    $fd = fopen($this->output, 'a');
    fputs($fd, date(DATE_RFC3339));
    fputs($fd, ' ');
    fputs($fd, $level);
    fputs($fd, ' : ');
    fputs($fd, $str);
    fputs($fd, "\r\n");
    fclose($fd);
  }
  
  function debug($str)
  {
    $this->write('DEBUG', $str);
  }
  
  function info($str)
  {
    $this->write('INFO ', $str);
  }
}