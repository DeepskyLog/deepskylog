<?php
if (!function_exists('fnmatch')) 
{
  function fnmatch($pattern, $string)
	{
    return @preg_match('/^' . strtr(addcslashes($pattern, '\\.+^$(){}=!<>|'), array('*' => '.*', '?' => '.?')) . '$/i', $string);
  }
}

if(!session_id()) session_start();
if(!array_key_exists('module',$_SESSION) || !$_SESSION['module'])
  $_SESSION['module'] = $modules[0];

require_once "../lib/setup/vars.php";
require_once "../lib/setup/databaseInfo.php";
require_once "../lib/setup/language.php";
require_once "../lib/util.php";
require_once "../lib/atlasses.php";
require_once "../lib/instruments.php";
require_once "../common/layout/tables.php";


// pagenumbers
if(array_key_exists('min',$_GET))
   $min=$_GET['min'];
elseif(array_key_exists('multiplepagenr',$_GET))
  $min = ($_GET['multiplepagenr']-1)*25;
elseif(array_key_exists('multiplepagenr',$_POST))
  $min = ($_POST['multiplepagenr']-1)*25;
else
  $min = 0;

?>
