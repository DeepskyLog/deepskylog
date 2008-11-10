<?php

// The observations class collects all functions needed to enter, retrieve and
// adapt observation data from the database.
//
// Version 0.8 : 12/09/2005, WDM
//

include_once "database.php";
include_once "instruments.php";
include_once "locations.php";
include_once "observers.php";
include_once "cometobjects.php";
include_once "setup/databaseInfo.php";

class CometObservations
{
 // addObservation adds a new observation to the database. The objectid, 
 // observerid, date and time should be given as parameters.
 function addObservation($objectid, $observerid, $date, $time)
 {
  $db = new database;
  $db->login();

  if (!$_SESSION['lang'])
  {
   $_SESSION['lang'] = "English";
  }

  $sql = "INSERT INTO cometobservations (objectid, observerid, date, time) VALUES (\"$objectid\", \"$observerid\", \"$date\", \"$time\")";
  mysql_query($sql) or die(mysql_error());


  $query = "SELECT id FROM cometobservations ORDER BY id DESC LIMIT 1";
  $run = mysql_query($query) or die(mysql_error());
  $get = mysql_fetch_object($run);
  $id = $get->id;

  $db->logout();

  return $id;
 }

 // deleteObservation($id) deletes the observation with the given id.
 function deleteObservation($id)
 {
  $db = new database;
  $db->login();

  $sql = "DELETE FROM cometobservations WHERE id=\"$id\"";
  mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getObjectId returns the objectid of the given observation
 function getObjectId($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $objectid = $get->objectid;

  $db->logout();

  return $objectid;
 }

 // setObjectId sets a new object for the given observation
 function setObjectId($id, $object)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET objectid = \"$object\" WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getObserverId returns the observerid of the given observation
 function getObserverId($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $observerid = $get->observerid;

  $db->logout();

  return $observerid;
 }

 // setObserverId sets a new observer for the given observation
 function setObserverId($id, $observer)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET observerid = \"$observer\" WHERE id = \"$id\" ";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getInstrumentId returns the instrumentid of the given observation
 function getInstrumentId($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $instrumentid = $get->instrumentid;

  $db->logout();

  return $instrumentid;
 }

 // setInstrumentId sets a new instrument for the given observation
 function setInstrumentId($id, $instrument)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET instrumentid = \"$instrument\" WHERE id = \"$id\" ";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getLocationId returns the locationid of the given observation
 function getLocationId($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $locationid = $get->locationid;

  $db->logout();

  return $locationid;
 }

 // setLocationId sets a new location for the given observation
 function setLocationId($id, $location)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET locationid = \"$location\" WHERE id = \"$id\" ";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getDate returns the date of the given observation
 function getDate($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $date = $get->date;

  $db->logout();

  return $date;
 }

 // setDate sets a new date for the given observation
 function setDate($id, $date)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET date = \"$date\" WHERE id = \"$id\" ";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // setLocalDateAndTime sets the date and time for the given observation 
 // when the time is given in  local time
 function setLocalDateAndTime($id, $date, $time)
 {
  include_once "locations.php";
  $locations = new Locations();

  if ($time >= 0)
  {
    $db = new database;
    $db->login();

    $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
    $run = mysql_query($sql) or die(mysql_error());

    $get = mysql_fetch_object($run);

    $location = $get->locationid;

    $db->logout();

    if ($location)
    {
      $timezone = $locations->getTimezone($location);

      $datearray = sscanf($date, "%4d%2d%2d");

      $dateTimeZone = new DateTimeZone($timezone);
      $date =  sprintf("%02d", $datearray[1]) . "/" . sprintf("%02d", $datearray[2]) . "/" . $datearray[0];

      $dateTime = new DateTime($date, $dateTimeZone);
      // Returns the timedifference in seconds
      $timedifference = $dateTimeZone->getOffset($dateTime); 
      $timedifference = $timedifference / 3600.0;

      $timestr = sscanf(sprintf("%04d", $time), "%2d%2d");

      $jd = cal_to_jd(CAL_GREGORIAN, $datearray[1], $datearray[2], $datearray[0]);

      $hours = $timestr[0] - (int)$timedifference;

      $timedifferenceminutes = ($timedifference - (int)$timedifference) * 60;

      $minutes = $timestr[1] - $timedifferenceminutes;

      if ($minutes < 0)
      {
        $hours = $hours - 1;
        $minutes = $minutes + 60;
      }
      else if ($minutes > 60)
      {
        $hours = $hours + 1;
        $minutes = $minutes - 60;
      }

      if ($hours < 0)
      { 
        $hours = $hours + 24;
        $jd = $jd - 1;
      }
      if ($hours >= 24)
      {
        $hours = $hours - 24;
        $jd = $jd + 1;
      }

      $time = $hours * 100 + $minutes;

      $dte = JDToGregorian($jd);
      sscanf($dte, "%2d/%2d/%4d", $month, $day, $year);
      $date = $year . sprintf("%02d", $month) . sprintf("%02d", $day);
    }
  }

  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET date = \"$date\" WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $sql = "UPDATE cometobservations SET time = \"$time\" WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getLocalDate returns the date of the given observation in local time
 function getLocalDate($id)
 {
  include_once "locations.php";
  $locations = new Locations();

  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  if($get)
  {
    $date = $get->date;

    $time = $get->time;
    $loc = $get->locationid;

    $db->logout();

    if($loc)
    {
      if($time >= 0)
      {
        $date = sscanf($date, "%4d%2d%2d");

        $timezone = $locations->getTimezone($loc);

        $dateTimeZone = new DateTimeZone($timezone);

        $datestr =  sprintf("%02d", $date[1]) . "/" . sprintf("%02d", $date[2]) . "/" . $date[0];

        $dateTime = new DateTime($datestr, $dateTimeZone);
        // Geeft tijdsverschil terug in seconden
        $timedifference = $dateTimeZone->getOffset($dateTime); 
        $timedifference = $timedifference / 3600.0;

        $jd = cal_to_jd(CAL_GREGORIAN, $date[1], $date[2], $date[0]);

        $time = sscanf(sprintf("%04d", $time), "%2d%2d");

        $hours = $time[0] + (int)$timedifference;
        $minutes = $time[1];

        // We are converting from UT to local time -> we should add the time difference!
        $timedifferenceminutes = ($timedifference - (int)$timedifference) * 60;

        $minutes = $minutes + $timedifferenceminutes;

        if ($minutes < 0)
        {
          $hours = $hours - 1;
          $minutes = $minutes + 60;
        }
        else if ($minutes > 60)
        {
          $hours = $hours + 1;
          $minutes = $minutes - 60;
        }

        if ($hours < 0)
        { 
          $hours = $hours + 24;
          $jd = $jd - 1;
        }
        if ($hours >= 24)
        {
          $hours = $hours - 24;
          $jd = $jd + 1;
        }

       $dte = JDToGregorian($jd);

       sscanf($dte, "%2d/%2d/%4d", $month, $day, $year);

       $date = sprintf("%d%02d%02d", $year, $month, $day);
      }
    }
  }

  return $date;
 }

 // getTime returns the Time of the given observation
 function getTime($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $time = $get->time;

  $db->logout();

  return $time;
 }

 // getLocalTime returns the time of the given observation in local time
 function getLocalTime($id)
 {
  include_once "locations.php";
  $locations = new Locations();

  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  if($get)
  {
    $date = $get->date;
    $time = $get->time;
    $loc = $get->locationid;

    $db->logout();

    if ($loc)
    {
      $date = sscanf($date, "%4d%2d%2d");

      $timezone = $locations->getTimezone($loc);

      $dateTimeZone = new DateTimeZone($timezone);

      $datestr =  sprintf("%02d", $date[1]) . "/" . sprintf("%02d", $date[2]) . "/" . $date[0];

      $dateTime = new DateTime($datestr, $dateTimeZone);
      // Geeft tijdsverschil terug in seconden
      $timedifference = $dateTimeZone->getOffset($dateTime); 
      $timedifference = $timedifference / 3600.0;

      if ($time < 0)
      {
        return $time;
      }
      $time = sscanf(sprintf("%04d", $time), "%2d%2d");

      $hours = $time[0] + (int)$timedifference;
      $minutes = $time[1];

      // We are converting from UT to local time -> we should add the time difference!
      $timedifferenceminutes = ($timedifference - (int)$timedifference) * 60;

      $minutes = $minutes + $timedifferenceminutes;

      if ($minutes < 0)
       {
        $hours = $hours - 1;
        $minutes = $minutes + 60;
      }
      else if ($minutes > 60)
      {
        $hours = $hours + 1;
        $minutes = $minutes - 60;
      }

      if ($hours < 0)
      { 
        $hours = $hours + 24;
      }
      if ($hours >= 24)
      {
        $hours = $hours - 24;
      }

      $time = $hours * 100 + $minutes;
    }
  }

  return $time;
 }

 // setTime sets a new time for the given observation
 function setTime($id, $time)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET time = \"$time\" WHERE id = \"$id\" ";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getDescription returns the Description of the given observation
 function getDescription($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $description = $get->description;

  $db->logout();

  return $description;
 }

 // setDescription sets a new Description for the given observation
 function setDescription($id, $description)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET description = \"$description\" WHERE id = \"$id\" ";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getMethode returns the Methode of the given observation
 function getMethode($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $methode = $get->methode;

  $db->logout();

  return $methode;
 }

 // setMethode sets a new Methode for the given observation
 function setMethode($id, $methode)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET methode = \"$methode\" WHERE id = \"$id\" ";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getMagnitude returns the Magnitude of the given observation
 function getMagnitude($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $magnitude = $get->mag;

  $db->logout();

  return $magnitude;
 }

 // setMagnitude sets a new Magnitude for the given observation
 function setMagnitude($id, $magnitude)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET mag = \"$magnitude\" WHERE id = \"$id\" ";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // setMagnitudeUncertain set the uncertain flag for the given magnitude
 function setMagnitudeUncertain($id, $magnitude)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET maguncertain = \"$magnitude\" WHERE id = \"$id\" ";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getMagnitudeUncertain returns 1 if the magnitude is uncertain
 function getMagnitudeUncertain($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $magnitude = $get->maguncertain;

  $db->logout();

  return $magnitude;
 }

 // setMagnitudeWeakerThan set the weaker than flag for the given magnitude
 function setMagnitudeWeakerThan($id, $magnitude)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET lessmagnitude = \"$magnitude\" WHERE id =
\"$id\" ";
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getMagnitudeWeakerThan returns 1 if the magnitude is weaker than the given magnitude
 function getMagnitudeWeakerThan($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $magnitude = $get->lessmagnitude;

  $db->logout();

  return $magnitude;
 }

 // getChart returns the Chart of the given observation
 function getChart($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $chart = $get->chart;

  $db->logout();

  return $chart;
 }

 // setChart sets a new Chart for the given observation
 function setChart($id, $chart)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET chart = \"$chart\" WHERE id = \"$id\" "
;
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getMagnification returns the Magnification of the given observation
 function getMagnification($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $magnification = $get->magnification;

  $db->logout();

  return $magnification;
 }

 // setMagnification sets a new Magnification for the given observation
 function setMagnification($id, $magnification)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET magnification = \"$magnification\" WHERE id = \"$id\" " ;
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getDc returns the Dc of the given observation
 function getDc($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $dc = $get->dc;

  $db->logout();

  return $dc;
 }

 // setDc sets a new Dc for the given observation
 function setDc($id, $dc)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET dc = \"$dc\" WHERE id = \"$id\" "
;
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getComa returns the Coma of the given observation
 function getComa($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $coma = $get->coma;

  $db->logout();

  return $coma;
 }

 // setComa sets a new Coma for the given observation
 function setComa($id, $coma)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET coma = \"$coma\" WHERE id = \"$id\" "
;
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getTail returns the Tail of the given observation
 function getTail($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $tail = $get->tail;

  $db->logout();

  return $tail;
 }

 // setTail sets a new Tail for the given observation
 function setTail($id, $tail)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET tail = \"$tail\" WHERE id = \"$id\" " ;
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getPa returns the Pa of the given observation
 function getPa($id)
 {
  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations WHERE id = \"$id\"";
  $run = mysql_query($sql) or die(mysql_error());

  $get = mysql_fetch_object($run);

  $pa = $get->pa;

  $db->logout();

  return $pa;
 }

 // setPa sets a new Pa for the given observation
 function setPa($id, $pa)
 {
  $db = new database;
  $db->login();

  $sql = "UPDATE cometobservations SET pa = \"$pa\" WHERE id = \"$id\" " ;
  $run = mysql_query($sql) or die(mysql_error());

  $db->logout();
 }

 // getObservations returns an array with all observations
 function getObservations()
 {
  include "setup/databaseInfo.php";
  $observers = new Observers;

  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations";

  $run = mysql_query($sql) or die(mysql_error());

  while($get = mysql_fetch_object($run))
  {
   $observations[] = $get->id;
  }

  $db->logout();

  if ($observations)
  {
   sort ($observations);
  }

  return $observations;
 }

 // getPopularObservers() returns the number of observations of the
 // observers
 function getPopularObservers()
 {
  include "setup/databaseInfo.php";
  $observers = new Observers;

  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations";
  $run = mysql_query($sql) or die(mysql_error());

  while($get = mysql_fetch_object($run))
  {
   $observations[] = $get->observerid;
  }
  $db->logout();

  if ($observations)
  {
   $numberOfObservations = array_count_values ($observations);

   arsort($numberOfObservations);

   return $numberOfObservations;
  }
  else
  {
   return null;
  }
 }

 // getNumberOfDifferentObjects() returns the number of different objects
 // observed
 function getNumberOfDifferentObjects()
 {
  include "setup/databaseInfo.php";
  include_once "observers.php";

  $observers = new Observers;
  $db = new database;
  $db->login();
  $sql = "SELECT COUNT(DISTINCT objectid) FROM cometobservations";

  $run = mysql_query($sql) or die(mysql_error());

  return mysql_result($run, 0, 0);
 }

 // getNumberOfObservations() returns the total number of observations
 function getNumberOfObservations()
 {
  include "setup/databaseInfo.php";
  include_once "observers.php";

  $observers = new Observers;

  $db = new database;
  $db->login();
  $sql = "SELECT COUNT(id) FROM cometobservations";

  $run = mysql_query($sql) or die(mysql_error());

  return mysql_result($run, 0, 0);
 }

 // getNumberOfObservationsThisYear() returns the number of observations this
 // year
 function getNumberOfObservationsThisYear()
 {
  include "setup/databaseInfo.php";
  include_once "observers.php";

  $observers = new Observers;

  $date = date("Y")."0101";

  $db = new database;
  $db->login();
  $sql = "SELECT COUNT(id) FROM cometobservations WHERE date > \"$date\"";

  $run = mysql_query($sql) or die(mysql_error());

  return mysql_result($run, 0, 0);
 }

 // getObservationsThisYear($id) returns the number of observations of the
 // observer the last year
 function getObservationsThisYear($id)
 {
  $date = date("Y")."0101";
  $q = array("observer" => $id, "mindate" => $date);
  $observations = $this->getObservationFromQuery($q);
  $numberOfObservations = count($observations);
  return $numberOfObservations;
 }

 // getNumberOfObjects($id) return the number of different objects seen by
 // the observer
 function getNumberOfObjects($id)
 {
  include "setup/databaseInfo.php";

  $db = new database;
  $db->login();
  $sql = "SELECT COUNT(DISTINCT objectid) FROM cometobservations WHERE observerid=\"$id\"";

  $run = mysql_query($sql) or die(mysql_error());

  return mysql_result($run, 0, 0);
 }

 // getPopularObservations() returns the number of observations of the
 // objects
 function getPopularObservations()
 {
  include "setup/databaseInfo.php";
  $observers = new Observers;

  $db = new database;
  $db->login();

  $sql = "SELECT * FROM cometobservations";
  $run = mysql_query($sql) or die(mysql_error());

  while($get = mysql_fetch_object($run))
  {
   $observations[] = $get->objectid;
  }
  $db->logout();

  $numberOfObservations = array_count_values ($observations);

  arsort($numberOfObservations);

  return $numberOfObservations;
 }

 // getSortedObservations returns an array with the ids of all observations,
 // sorted by the column specified in $sort
 function getSortedObservations($sort)
 {
  include "setup/databaseInfo.php";
  $observers = new Observers;

  $db = new database;
  $db->login();

  if ($sort == "date")
  {
   $sort = "date, time";
  }

  if ($sort == "objectname")
  {
   $sql = "SELECT cometobservations.id FROM cometobservations LEFT JOIN cometobjects on cometobservations.objectid=cometobjects.id ORDER BY cometobjects.name ";

   $run = mysql_query($sql) or die(mysql_error());
   $get = mysql_fetch_object($run);
  }
  else
  {
   if ($sort=="inst")
   {
    $sort = " instruments.diameter, instruments.id";
   }
   else if ($sort=="observerid")
   {
    $sort = " observers.name, observers.firstname";
   }
   $sql = "SELECT cometobservations.* FROM cometobservations LEFT JOIN instruments on cometobservations.instrumentid=instruments.id LEFT JOIN observers on cometobservations.observerid=observers.id ORDER BY $sort";
  }

  $run = mysql_query($sql) or die(mysql_error());

  while($get = mysql_fetch_object($run)) {
   $observations[] = $get->id;
  }

  $db->logout();

  return $observations;
 }

 // getObservationFromQuery returns an array with the names of all observations
 //  where the queries are defined in an array.
 // An example of an array :
 //  $q = array("object" => "NGC 7293", "observer" => "wim",
 //             "instrument" => "3", "location" => "24",
 //             "mindate" => "20040512", "maxdate" => "20040922",
 //             "minmag" => "6.0", "maxmag" => "14.0",
 //             "description" => "Doughnut", "mindc" => "4",
 //             "maxdc" => "6", "mincoma" => "12.0", "maxcoma" => "15.0", 
 //             "mintail" => "11.0", "maxtail" => "15.0");
 function getObservationFromQuery($queries, $sort = "", $exactmatch = "1", $clubOnly = "True")
 {
  include "setup/databaseInfo.php";
  $observers = new Observers;
  $objects = new CometObjects;
  $locations = new Locations;

  $object = "";
  $sqland = 0;

  if (array_key_exists("object", $queries) && ($queries["object"] != ""))
  {
   if ($exactmatch == "1")
   {
    $alternative = $objects->getName($queries["object"]);
   }
   else
   {
    $objectList = $objects->getNameList($queries["object"]);
   }

   if ($object == "")
   {
    $object = $queries["object"];
   }
  }


  $db = new database;
  $db->login();

  $sql = "SELECT cometobservations.* FROM cometobservations LEFT JOIN instruments on cometobservations.instrumentid=instruments.id LEFT JOIN cometobjects on cometobservations.objectid=cometobjects.id LEFT JOIN observers on cometobservations.observerid=observers.id where";
 
  if ($object != "")
  {
   if ($exactmatch == "1")
   {
    $sql = $sql." (cometobjects.name = \"$object\")";
   }
   else
   {
    $sql = $sql." (cometobjects.name like \"%$object%\"";
    if ($objectList == "")
    {
     $sql = $sql.")";
    }
    else
    {
     while(list($key, $value) = each($objectList))
     {
      $sql = $sql." or cometobjects.name = \"$value\"";
     }
     $sql = $sql.")";
    }
   }
   $sqland = 1;
  }
  if (isset($queries["observer"]) && $queries["observer"] != "")
  {
   $observer = $queries["observer"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." cometobservations.observerid = \"$observer\"";
   $sqland = 1;
  }

  if (array_key_exists("instrument", $queries) && ($queries["instrument"] != ""))
  {
   $inst = $queries["instrument"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." cometobservations.instrumentid = \"$inst\"";
   $sqland = 1;
  }

  if (array_key_exists("mindiameter", $queries) && ($queries["mindiameter"] != ""))
  {
   $diam = $queries["mindiameter"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." instruments.diameter >= \"$diam\"";
   $sqland = 1;
  }

  if (array_key_exists("maxdiameter", $queries) && ($queries["maxdiameter"] != ""))
  {
   $diam = $queries["maxdiameter"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." instruments.diameter <= \"$diam\"";
   $sqland = 1;
  }

  if (isset($queries["location"]) && ($queries["location"] != ""))
  {
   $locs = $locations->getAllLocationsIds($queries["location"]);

   $loc = $queries["location"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }

   $sql = $sql." (cometobservations.locationid = \"$locs[0]\"";

   $i = 1;
   while ($i < count($locs))
   {
     $sql = $sql." || cometobservations.locationid = \"$locs[$i]\"";
     $i = $i + 1;
   }
   $sql = $sql.") ";

   $sqland = 1;
  }

  if (array_key_exists("maxdate", $queries) && ($queries["maxdate"] != ""))
  {
   $date = $queries["maxdate"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." cometobservations.date <= \"$date\"";
   $sqland = 1;
  }

  if (array_key_exists("mindate", $queries) && ($queries["mindate"] != ""))
  {
   $date = $queries["mindate"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." cometobservations.date >= \"$date\"";
   $sqland = 1;
  }

  if (array_key_exists("description", $queries) && ($queries["description"] != ""))
  {
   $description = $queries["description"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." cometobservations.description like \"%$description%\"";
   $sqland = 1;
  }

  if (array_key_exists("minmag", $queries) && (strcmp($queries["minmag"], "") != 0))
  {
   $mag = $queries["minmag"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." (cometobservations.mag > $mag or cometobservations.mag like $mag)";
   $sqland = 1;
  }

  if (array_key_exists("maxmag", $queries) && (strcmp($queries["maxmag"], "") != 0))
  {
   $mag = $queries["maxmag"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." (cometobservations.mag < $mag or cometobservations.mag like $mag)";
   $sqland = 1;
  }

  if (array_key_exists("maxdc", $queries) && ($queries["maxdc"] != ""))
  {
   $dc = $queries["maxdc"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." cometobservations.dc <= $dc";
   $sqland = 1;
  }

  if (array_key_exists("mindc", $queries) && ($queries["mindc"] != ""))
  {
   $dc = $queries["mindc"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." cometobservations.dc >= $dc";
   $sqland = 1;
  }

  if (array_key_exists("maxcoma", $queries) && ($queries["maxcoma"] != ""))
  {
   $coma = $queries["maxcoma"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." cometobservations.coma <= $coma";
   $sqland = 1;
  }

  if (array_key_exists("mincoma", $queries) && ($queries["mincoma"] != ""))
  {
   $coma = $queries["mincoma"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." cometobservations.coma >= $coma";
   $sqland = 1;
  }

  if (array_key_exists("maxtail", $queries) && ($queries["maxtail"] != ""))
  {
   $tail = $queries["maxtail"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." cometobservations.tail <= $tail";
   $sqland = 1;
  }

  if (array_key_exists("mintail", $queries) && ($queries["mintail"] != ""))
  {
   $tail = $queries["mintail"];
   if ($sqland == 1)
   {
    $sql = $sql." and";
   }
   $sql = $sql." cometobservations.tail >= $tail";
   $sqland = 1;
  }

  if ($sort != "")
  {
   if ($sort=="inst")
   {
    $sort = "instruments.diameter, instruments.id";
   }
   if ($sort == "observerid")
   {
    $sort = "observers.name, observers.firstname";
   }
   if ($sort == "date")
   {
    $sort = $sort.", time";
   }
   else if ($sort == "objectid")
   {
    $sort = "cometobjects.name";
   }
 
   $sql = $sql." order by $sort";
  }

  $sql = $sql.";";

  $run = mysql_query($sql) or die(mysql_error());

  while($get = mysql_fetch_object($run))
  {
   $obs[] = $get->id;
  }

  $db->logout();

  if(isset($obs))
  {
    return $obs;
  }
  else
  {
    return null;
  }
 }
}
