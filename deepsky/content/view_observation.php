<?php
// view_observation.php
// view information of observation 

if(!array_key_exists('observation',$_GET)||!$_GET['observation']) //  
   throw new Exception ("No observation defined in view_observation.php");
if(!($object=$GLOBALS['objObservation']->getObjectId($_GET['observation'])))    // check if observation exists
   throw new Exception ("No observed object found in view_observation.php");
echo "<table width=\"100%\">";
echo "<tr>";
echo "<td>";
echo "<div id=\"main\">";
echo "<h2>".LangViewObservationTitle;
$seen=$GLOBALS['objObject']->getDSOseen($object);
echo "&nbsp;-&nbsp;".stripslashes($object);
echo "&nbsp;-&nbsp;".LangOverviewObjectsHeader7.":&nbsp;".$seen;
echo "</h2>";
echo "</td>";
echo "<td align=\"right\">";
if(array_key_exists('Qobs',$_SESSION)&&count($_SESSION['Qobs'])&&array_key_exists('QobsKey',$_GET))                // array of observations
{ if($_GET['QobsKey']>0)
    echo "&nbsp;<a href=\"deepsky/index.php?indexAction=detail_observation&amp;observation=".$_SESSION['Qobs'][$_GET['QobsKey']-1]['observationid']."&amp;QobsKey=".($_GET['QobsKey']-1)."&amp;dalm=".$_GET['dalm']."\" title=\"".LangPreviousObservation."\">"."<img src=\"".$baseURL."/styles/images/left20.gif\" border=\"0\">"."</a>&nbsp;&nbsp;";
  if($_GET['QobsKey']<(count($_SESSION['Qobs'])-1))
    echo "&nbsp;<a href=\"deepsky/index.php?indexAction=detail_observation&amp;observation=".$_SESSION['Qobs'][$_GET['QobsKey']+1]['observationid']."&amp;QobsKey=".($_GET['QobsKey']+1)."&amp;dalm=".$_GET['dalm']."\" title=\"".LangNextObservation."\">"."<img src=\"".$baseURL."/styles/images/right20.gif\" border=\"0\">"."</a>";
}
echo "</td>";
echo "</tr>";
echo "</table>";

echo "<table width=\"100%\"><tr>";
echo("<td width=\"25%\" align=\"left\">");
echo("<a href=\"deepsky/index.php?indexAction=detail_object&object=" . urlencode($object) . "\">" . LangViewObjectViewNearbyObject . " " . $object);
echo("</td><td width=\"25%\" align=\"center\">");
if (array_key_exists('deepskylog_id', $_SESSION) && ($_SESSION['deepskylog_id']!=""))
  echo("<a href=\"deepsky/index.php?indexAction=add_observation&object=" . urlencode($object) . "\">" . LangViewObjectAddObservation . $object . "</a>");
echo("</td>");
if($myList)
{ echo("<td width=\"25%\" align=\"center\">");
  if($list->checkObjectInMyActiveList($object))
    echo("<a href=\"deepsky/index.php?indexAction=result_selected_observations&amp;object=" . urlencode($object) . "&amp;removeObjectFromList=" . urlencode($object) . "\">" . $object . LangListQueryObjectsMessage3 . $_SESSION['listname'] . "</a>");
  else
    echo("<a href=\"deepsky/index.php?indexAction=result_selected_observations&amp;object=" . urlencode($object) . "&amp;addObjectToList=" . urlencode($object) . "&amp;showname=" . urlencode($object) . "\">" . $object . LangListQueryObjectsMessage2 . $_SESSION['listname'] . "</a>");
 echo("</td>");
}	
echo("</tr>");
echo("</table>");

$GLOBALS['objObject']->showObject($object);
if(array_key_exists('deepskylog_id', $_SESSION) && $_SESSION['deepskylog_id'])                  // LOGGED IN
{ if($_GET['dalm']!="D")
	{
		echo("<a href=\"deepsky/index.php?indexAction=detail_observation&observation=" . $_GET['observation'] . "&dalm=D\" title=\"" . LangDetail . "\">");
      echo(LangDetailText); 
	  echo("</a>");
	  echo("&nbsp;");
	}
	if($_GET["dalm"]!="AO")
	{
	  echo("<a href=\"deepsky/index.php?indexAction=detail_observation&observation=" . $_GET['observation'] . "&dalm=AO\" title=\"" . LangAO . "\">");
      echo(LangAOText); 
	  echo("</a>");
	  echo("&nbsp;");
	}
	if ($GLOBALS['objObservation']->getObservationsUserObject($_SESSION['deepskylog_id'], $object)>0)
{
		if($_GET['dalm']!="MO")
	{
	  echo("<a href=\"deepsky/index.php?indexAction=detail_observation&observation=" . $_GET['observation'] . "&dalm=MO\" title=\"" . LangMO . "\">");
        echo(LangMOText); 
     echo("</a>&nbsp;");
   }
	if($_GET['dalm']!="LO")
	{
	  echo("<a href=\"deepsky/index.php?indexAction=detail_observation&observation=" . $_GET['observation'] . "&dalm=LO\" title=\"" . LangLO . "\">");
        echo(LangLOText); 
     echo("</a>&nbsp;");
    }
 }
 echo(LangOverviewObservationsHeader5a);
 echo "<hr>";
}

$GLOBALS['objObservation']->showObservation($_GET['observation']);

if($_GET['dalm']=="AO") $AOid = $GLOBALS['objObservation']->getAOObservationsId($object, $_GET['observation']);
elseif($_GET['dalm']=="MO") $AOid = $GLOBALS['objObservation']->getMOObservationsId($object, $_SESSION['deepskylog_id'], $_GET['observation']);
elseif($_GET['dalm']=="LO") $AOid = array($GLOBALS['objObservation']->getLOObservationId($object, $_SESSION['deepskylog_id'], $_GET['observation']));
else $AOid=array();
while(list($key, $LOid) = each($AOid)) 
 $GLOBALS['objObservation']->showObservation($LOid);
?>
