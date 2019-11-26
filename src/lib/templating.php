<?php
$trads_dir = dirname(__FILE__).'/../../config/';
$lang = @$_REQUEST['lang'];

$_T = array();
global $T;
if ($lang == '')
{
  $languages = @$_SERVER['HTTP_ACCEPT_LANGUAGE'];
  if ($languages != '')
  {
    $lgs = preg_split('/;|,|-/', $languages);
    foreach($lgs as $lg)
    {
      $lg = strtolower(substr($lg, 0, 2));
      if (file_exists("$trads_dir/trad.$lg.php"))
      {
        include_once("$trads_dir/trad.$lg.php");
        break;
      }
    }
  }
  else
    include_once("$trads_dir/trad.en.php");
}



function T($key, $escaping = FALSE)
{
  global $_T;
  global $lang;
  if (isset($_T[$key])) {
    $v = $_T[$key];
  }
  else
  {
    $v ="!! $key not translated in lang $lang!!";
  }
  if ($escaping === true)
    return htmlentities($v);
  else
    return $v;
}