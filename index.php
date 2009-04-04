<?php // index.php - main entrance to DeepskyLog
try
{ $inIndex=true;
  if(!array_key_exists('indexAction',$_GET)&&array_key_exists('indexAction',$_POST)) 
    $_GET['indexAction']=$_POST['indexAction'];
  include 'common/entryexit/preludes.php';                                             // Includes of all classes and assistance files
  include 'common/entryexit/instructions.php';                                         // Execution of all non-layout related instructions (login, add objects to lists, etc.)
  include 'common/layout/presentation.php';
  include 'common/menu/head.php';                                                      // HTML head
  echo    "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
  echo    "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
  echo    "<body id=\"dslbody\" style=\"position:relative;margin:0px;\"  onresize=\"resizeForm();\">";
  include 'common/menu/headmenu.php';                                                  // Page Title and welcome line - modules choices
  echo    "<div id=\"div3\">";
  include 'common/entryexit/menu.php';                                                 // Left Menu Section
  echo    "</div>";
  echo    "<div id=\"div4\">";
  echo    "<img id=\"div4a\" src=\"".$baseURL."styles/images/lu.gif\">";
  echo    "<img id=\"div4b\" src=\"".$baseURL."styles/images/lo.gif\">";
  echo    "</div>";
  echo    "<div id=\"div5\">"; 
  $includeFile=$objUtil->utilitiesDispatchIndexAction();                               // Determine the page to show
  include 'common/entryexit/data.php';                                                 // Get data for the form, object data, observation data, etc.
  include $includeFile;                                                                // Center content section	<<<===============================================================
//  $_SESSION['ifrm']=$includeFile;
//  echo "<iframe name=\"ifrm_cont\" id=\"ifrm_cont\" src=\"".$baseURL."ifrm_holder.php?link=".urlencode($link)."&amp;link2=".urlencode($link2)."&amp;min=".$min."&amp;max=".$max."&amp;expand=".$objUtil->checkGetKey('expand')."\" frameborder=\"0\" width=\"100%\" style=\"height:400px\">";
//	include $includeFile;   
//	echo "</iframe>";
//	$resizeElement="ifrm_cont";
//	$resizeSize=80;
  echo    "</div>";
  echo    "<div id=\"div6\">";
  echo    "<img id=\"div6a\" src=\"".$baseURL."styles/images/rU.gif\">";                       // Right white bar
  echo    "<img id=\"div6b\" src=\"".$baseURL."styles/images/ro.gif\">";                       // Right white bar
  echo    "</div>";
  echo    "<div id=\"div7\">";	
  echo    "Copyright 2004 - 2008&nbsp;";                                               // bottom line
  echo    "<a href=\"http://www.vvs.be\">Vereniging voor Sterrenkunde</a> - Powered by&nbsp;";
  echo    "<a href=\"http://www.deepskylog.org\">DeepskyLog</a>&nbsp;".$versionInfo;   //defined in databaseInfo.php
  echo    "</div>";
  if(isset($entryMessage)&&$entryMessage)                                              // Entry Message if any
    echo  "<script>alert('".addslashes(html_entity_decode($entryMessage))."');</script>";
}
catch (Exception $e)
{ echo    "<p>DeepskyLog encounterd a problem. Could you please report it to the Developers?</p>";
  echo    "<p>Report problem with error message: " . $e->getMessage()."</p>";
  echo    "<p>You can report the problem by sending an email to developers@deepskylog.be.</p>";
  echo    "<p>Thank you.</p>";
  // EMAIL developers with error codes
}
echo "</body>";
echo "<script>";
echo "function resizeForm()";
echo "{";
echo "document.getElementById('div5').style.width=(document.getElementById('dslbody').clientWidth-185)+'px';";
echo "document.getElementById('div6').style.left=(document.getElementById('dslbody').clientWidth-16)+'px';";
echo "document.getElementById('div7').style.width=(document.getElementById('dslbody').clientWidth-169)+'px';";
echo "document.getElementById('div4').style.height=(document.getElementById('dslbody').clientHeight-111)+'px';";
echo "document.getElementById('div5').style.height=(document.getElementById('dslbody').clientHeight-111)+'px';";
echo "document.getElementById('div6').style.height=(document.getElementById('dslbody').clientHeight-111)+'px';";
echo "document.getElementById('div7').style.top=(document.getElementById('dslbody').clientHeight-26)+'px';";
if($resizeElement)
  echo "resizeElement('".$resizeElement."',".$resizeSize.");";
echo "}";
echo "resizeForm();";
echo "</script>";
echo "</html>";
?>
