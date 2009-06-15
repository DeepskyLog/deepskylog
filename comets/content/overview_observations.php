<?php
// overview_observations.php
// generates an overview of all observations in the database

$objects = new CometObjects;
$instruments = new Instruments;
$observers = new Observers;

if(isset($_GET['sort'])) // field to sort on given as a parameter in the url
{ $sort = $_GET['sort'];
  $obs = $objCometObservation->getSortedObservations($sort);
}
else
{ $sort = "date"; // standard sort on date
  $obs = $objCometObservation->getSortedObservations($sort);
  if(sizeof($obs) > 0)
    krsort($obs);
}
// save $obs as a session variable
$_SESSION['obs'] = $obs;
$_SESSION['observation_query'] = $obs;

echo "<div id=\"main\">";
if(isset($_GET['previous']))
  $previous = $_GET['previous'];
else
  $previous = 'date';
$count = 0; // counter for altering table colors
$link = $baseURL."index.php?indexAction=comets_all_observations&amp;sort=".$sort."&amp;previous=".$previous;
if(isset($_GET['sort']) && isset($_GET['previous']) && ($_GET['previous'] == $_GET['sort'])) // reverse sort when pushed twice
{ if(sizeof($obs) > 0)
    krsort($obs);
  $previous = ""; // reset previous field to sort on
}
else
  $previous = $sort;
if((array_key_exists('steps',$_SESSION))&&(array_key_exists("selComObs",$_SESSION['steps'])))
	$step=$_SESSION['steps']["selComObs"];
if(array_key_exists('multiplepagenr',$_GET))
  $min = ($_GET['multiplepagenr']-1)*$step;
elseif(array_key_exists('multiplepagenr',$_POST))
  $min = ($_POST['multiplepagenr']-1)*$step;
elseif(array_key_exists('min',$_GET))
  $min=$_GET['min'];
else
  $min = 0;
list($min,$max,$content)=$objUtil->printNewListHeader3($obs, $link, $min, $step);
$objPresentations->line(array("<h4>".LangOverviewObservationsTitle."</h4>",$content),"LR",array(50,50),30);
$content=$objUtil->printStepsPerPage3($link,"selComObs",$step);
$objPresentations->line(array($content),"R",array(100),20);
echo "<hr />";


if(sizeof($obs) > 0)
{
// OBJECT TABLE HEADERS

echo "<table style=\"width:100%\">
      <tr class=\"type3\">
      <td><a href=\"".$baseURL."index.php?indexAction=comets_all_observations&amp;sort=objectname&amp;previous=$previous\">" . LangOverviewObservationsHeader1 . "</a></td>
      <td><a href=\"".$baseURL."index.php?indexAction=comets_all_observations&amp;sort=observerid&amp;previous=$previous\">" . LangOverviewObservationsHeader2 . "</a></td>
      <td><a href=\"".$baseURL."index.php?indexAction=comets_all_observations&amp;sort=date&amp;previous=$previous\">" . LangOverviewObservationsHeader4 . "</a></td>
      <td><a href=\"".$baseURL."index.php?indexAction=comets_all_observations&amp;sort=mag&amp;previous=$previous\">" . LangNewComet1 . "</a></td>
      <td><a href=\"".$baseURL."index.php?indexAction=comets_all_observations&amp;sort=inst&amp;previous=$previous\">" . LangViewObservationField3 . "</a></td>
      <td><a href=\"".$baseURL."index.php?indexAction=comets_all_observations&amp;sort=coma&amp;previous=$previous\">" . LangViewObservationField19 . "</a></td>
      <td><a href=\"".$baseURL."index.php?indexAction=comets_all_observations&amp;sort=dc&amp;previous=$previous\">" . LangViewObservationField18b . "</a></td>
      <td><a href=\"".$baseURL."index.php?indexAction=comets_all_observations&amp;sort=tail&amp;previous=$previous\">" . LangViewObservationField20b . "</a></td>
      <td></td>
      </tr>";
while(list ($key, $value) = each($obs)) // go through observations array
{ if($count >= $min && $count < $max)
   { if ($count % 2)
      { $typefield = "class=\"type1\"";
      }
      else
      { $typefield = "class=\"type2\"";
      }

      // OBJECT 

      $object = $objCometObservation->getObjectId($value);

      // OBSERVER 

      $observer = $objCometObservation->getObserverId($value);

      // DATE

      if ($objObserver->getObserverProperty($loggedUser,'UT'))
      {
       $date = sscanf($objCometObservation->getDate($value), "%4d%2d%2d");
      }
      else
      {
       $date = sscanf($objCometObservation->getLocalDate($value), "%4d%2d%2d");
      }

      // TIME
      if ($objObserver->getObserverProperty($loggedUser,'UT'))
      {
       $time = sscanf(sprintf("%04d", $objCometObservation->getTime($value)), "%2d%2d");
      }
      else
      {
       $time = sscanf(sprintf("%04d", $objCometObservation->getLocalTime($value)), "%2d%2d");
      }

      // INSTRUMENT 
 
      $temp = $objCometObservation->getInstrumentId($value);
      $instrument = $objInstrument->getInstrumentPropertyFromId($temp,'name');
      $instrumentsize = round($objInstrument->getInstrumentPropertyFromId($temp,'diameter'), 0);
      if ($instrument == "Naked eye")
      {
       $instrument = InstrumentsNakedEye;
      }

      // MAGNITUDE

      $mag = $objCometObservation->getMagnitude($value);

      if ($mag < -90)
      {
       $mag = '';
      }
      else
      {
       $mag = sprintf("%01.1f", $mag);
       if($objCometObservation->getMagnitudeWeakerThan($value) == "1")
       {
         $mag = "[" . $mag;
       }
       if($objCometObservation->getMagnitudeUncertain($value) == "1")
       {
         $mag = $mag . ":";
       }
      }

      // COMA

      $coma = $objCometObservation->getComa($value);
      if ($coma < -90)
      {
       $coma = '';
      }
      else
      {
       $coma = $coma."'";
      }

      // DC

      $dc = $objCometObservation->getDc($value);

      if ($dc < -90)
      {
       $dc = '';
      }

      // TAIL

      $tail = $objCometObservation->getTail($value);
      if ($tail < -90)
      {
       $tail = '';
      }
      else
      {
       $tail = $tail."'";
      }

      // OUTPUT

      echo("<tr $typefield>\n
            <td><a href=\"".$baseURL."index.php?indexAction=comets_detail_object&amp;object=" . urlencode($object) . "\">" . $objCometObject->getName($object) . "</a></td>\n
            <td><a href=\"".$baseURL."index.php?indexAction=detail_observer&amp;user=" . urlencode($observer) . "\">" . $objObserver->getObserverProperty($observer,'firstname') . "&nbsp;" . $objObserver->getObserverProperty($observer,'name') . 
            "</a></td>\n<td>");

      echo date ($dateformat, mktime (0,0,0,$date[1],$date[2],$date[0]));

      echo ("&nbsp;(");

      printf("%02d", $time[0]);

      echo (":");

      printf("%02d", $time[1]);

      if($instrument != InstrumentsNakedEye && $instrumentsize != "0" && $instrumentsize != "1")
      {
         $instrument = $instrument. "(" . $instrumentsize . "&nbsp;mm" . ")";
      }

      echo(")</td>\n
            <td>$mag</td>
            <td>$instrument</td>
            <td>$coma</td>
            <td>$dc</td>
            <td>$tail</td>
            <td><a href=\"".$baseURL."index.php?indexAction=comets_detail_observation&amp;observation=" . $value . "\">details");

      // LINK TO DRAWING (IF AVAILABLE)

$upload_dir = 'cometdrawings';
$dir = opendir($instDir.'comets/'.$upload_dir);
while (FALSE !== ($file = readdir($dir)))
{ if ("." == $file OR ".." == $file)
    continue; // skip current directory and directory above
  if(fnmatch($value . "_resized.gif", $file) || fnmatch($value . "_resized.jpg", $file) || fnmatch($value. "_resized.png", $file))
  { echo("&nbsp;+&nbsp;");
    echo LangDrawing;
  }
}
 
   echo("</a></td>\n</tr>\n");

   }

   $count++; // increase counter
}

echo "</table>";
}
echo "<hr />";
echo "</div>";

?>
