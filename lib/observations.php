<?php // The observations class collects all functions needed to enter, retrieve and adapt observation data from the database.
interface iObservations
{ public  function addDSObservation($objectname, $observerid, $instrumentid, $locationid, $date, $time, $description, $seeing, $limmag, $visibility, $language);      // adds an observation to the db
  public  function getAllInfoDsObservation($id);                                                                                                                      // returns all information of an observation
	public  function getAOObservationsId($object, $notobservation); 
  public  function getDsObservationLocalDate($id);                                                                                                                    // returns the date of the given observation in local time
	public  function getDsObservationLocalTime($id);                                                                                                                    // returns the time of the given observation in local time
	public  function getDsObservationProperty($id, $property, $defaultvalue='');                                                                                        // returns the property of the observation
	public  function getDsObservationsCountFromObserver($id);                                                                                                           // returns the number of observations entered by the observer with visibilty != 7                                                                                  
	public  function getLOObservationId($objectname, $userid, $notobservation); 
	public  function getMaxObservation();
	public  function getMOObservationsId($object, $userid, $notobservation); 
	public  function getNumberOfDifferentObservedDSObjects();	                                                                                                          // returns the number of different objects observed
	public  function getNumberOfDsObservations();                                                                                                                       // returns the total number of observations
	public  function getNumberOfObjects($id);                                                                                                                           // return the number of different objects seen by the observer
	public  function getObjectsFromObservations($observations,$showPartOfs=0); 
	public  function getObservationFromQuery($queries, $seenpar = "D", $exactinstrumentlocation = "0");                                                                 // returns an array with the names of all observations where the queries are defined in an array. 
	public  function getObservationsLastYear($id); 
	public  function getObservationsUserObject($userid, $object); 
	public  function getObservedCountFromCatalogOrList($id, $catalog); 
	public  function getObservedFromCatalog($id, $catalog); 
  public  function getObservedFromCatalogPartOf($id, $catalog); 
	public  function getPopularObservations();                                                                                                                          // returns the number of observations of the objects
  public  function getPopularObservers();                                                                                                                             // returns the number of observations of the observers
	public  function getPopularObserversOverviewCatOrList($sort, $cat = ""); 
  public  function setDsObservationProperty($id,$property,$propertyValue);                                                                                            // sets the property to the specified value for the given observation
	public  function setLocalDateAndTime($id, $date, $time);                                                                                                          	// sets the date and time for the given observation when the time is given in  local time
  public  function validateDeleteDSObservation($id);                                                                                                                  // removes the observation with id = $id
}
class Observations {
	public  function addDSObservation($objectname, $observerid, $instrumentid, $locationid, $date, $time, $description, $seeing, $limmag, $visibility, $language) 
	{ // adds a new observation to the database. The name, observerid, instrumentid, locationid, date, time, description, seeing and limiting magnitude should be given as parameters. The id of the latest observation is returned.
	  // If the time and date are given in local time, you should execute setLocalDateAndTime after inserting the observation!
		global $objDatabase;
		if(($seeing=="-1")||($seeing==""))
			$seeing="NULL";
		if($limmag=="")
			$limmag="NULL";
		else 
		{ if (ereg('([0-9]{1})[.,]([0-9]{1})', $limmag, $matches))   // limiting magnitude like X.X or X,X with X a number between 0 and 9
				$limmag=$matches[1].".".$matches[2];    // valid limiting magnitude // save current magnitude limit
			$limmag="$limmag";
		}
		$description = html_entity_decode($description, ENT_COMPAT, "ISO-8859-15");
		$description = preg_replace("/(\")/", "", $description);
		$description = preg_replace("/;/", ",", $description);
		$objDatabase->execSQL("INSERT INTO observations (objectname, observerid, instrumentid, locationid, date, time, description, seeing, limmag, visibility, language) " .
		                      "VALUES (\"$objectname\", \"$observerid\", \"$instrumentid\", \"$locationid\", \"$date\", \"$time\", \"$description\", $seeing, $limmag, $visibility, \"$language\")");
		return $objDatabase->selectSingleValue("SELECT id FROM observations ORDER BY id DESC LIMIT 1", 'id');
	}
  public  function getAllInfoDsObservation($id)                                                                                                                       // returns all information of an observation
	{ global $objDatabase;
		$get = mysql_fetch_object($objDatabase->selectRecordset("SELECT * FROM observations WHERE id=\"$id\""));
		$ob["name"] = $get->objectname;
		$ob["observer"] = $get->observerid;
		$ob["instrument"] = $get->instrumentid;
		$ob["location"] = $get->locationid;
		$ob["date"] = $get->date;
		$ob["time"] = $get->time;
		$ob["description"] = $get->description;
		$ob["seeing"] = $get->seeing;
		$ob["limmag"] = $get->limmag;
		$ob["visibility"] = $get->visibility;
		$ob["localdate"] = $this->getDsObservationLocalDate($id);
		$ob["localtime"] = $this->getDsObservationLocalTime($id);
		$ob["language"] = $this->getDsObservationProperty($id,'language');
		$ob["eyepiece"] = $get->eyepieceid;
		$ob["filter"] = $get->filterid;
		$ob["lens"] = $get->lensid;
		$ob["sqm"] = $get->SQM;
		$ob["largeDiam"] = $get->largeDiameter;
		$ob["smallDiam"] = $get->smallDiameter;
		$ob["stellar"] = $get->stellar;
		$ob["extended"] = $get->extended;
		$ob["resolved"] = $get->resolved;
		$ob["mottled"] = $get->mottled;
		$ob["characterType"] = $get->characterType;
		$ob["unusualShape"] = $get->unusualShape;
		$ob["partlyUnresolved"] = $get->partlyUnresolved;
		$ob["colorContrasts"] = $get->colorContrasts;
		return $ob;
	}
	public  function getAOObservationsId($object, $notobservation) 
  { global $objDatabase; 
		return $objDatabase->selectSingleArray("SELECT observations.id FROM observations WHERE objectname=\"".$object."\" AND id!=\"".$notobservation."\" ORDER BY id DESC",'id');
	}
	public  function getDsObservationLocalDate($id)                                                                                                                     // returns the date of the given observation in local time
	{ global $objDatabase, $objLocation;
		$run = $objDatabase->selectRecordset("SELECT date,time,locationid FROM observations WHERE id=\"".$id."\"");
		if($get=mysql_fetch_object($run)) 
		{ $date=$get->date;
			$time=$get->time;
			$loc=$get->locationid;
			if($time>=0)
			{ $date=sscanf($get->date,"%4d%2d%2d");
				$timezone=$objLocation->getLocationPropertyFromId($get->locationid,'timezone');
				$dateTimeZone=new DateTimeZone($timezone);
				$datestr=sprintf("%02d",$date[1])."/".sprintf("%02d",$date[2])."/".$date[0];
				$dateTime = new DateTime($datestr, $dateTimeZone);
				// Geeft tijdsverschil terug in seconden
				$timedifference = $dateTimeZone->getOffset($dateTime);
				$timedifference = $timedifference / 3600.0;
				$jd = cal_to_jd(CAL_GREGORIAN, $date[1], $date[2], $date[0]);
				$time = sscanf(sprintf("%04d", $time), "%2d%2d");
				$hours = $time[0] + (int) $timedifference;
				$minutes = $time[1];
				// We are converting from UT to local time -> we should add the time difference!
				$timedifferenceminutes = ($timedifference - (int) $timedifference) * 60;
				$minutes = $minutes + $timedifferenceminutes;
				if ($minutes < 0) {
					$hours = $hours -1;
					$minutes = $minutes +60;
				} else
					if ($minutes > 60) {
						$hours = $hours +1;
						$minutes = $minutes -60;
					}
				if ($hours < 0) {
					$hours = $hours +24;
					$jd = $jd -1;
				}
				if ($hours >= 24) {
					$hours = $hours -24;
					$jd = $jd +1;
				}
				$dte = JDToGregorian($jd);
				sscanf($dte, "%2d/%2d/%4d", $month, $day, $year);
				$date = sprintf("%d%02d%02d", $year, $month, $day);
			}
			return $date;
		}
	}
	public  function getDsObservationLocalTime($id)                                                                                                 // returns the time of the given observation in local time
	{ if ($get = mysql_fetch_object($GLOBALS['objDatabase']->selectrecordset("SELECT date, time, locationid FROM observations WHERE id=\"$id\""))) {
			$date = $get->date;
			$time = $get->time;
			$loc = $get->locationid;
			$date = sscanf($date, "%4d%2d%2d");
			$timezone = $GLOBALS['objLocation']->getLocationPropertyFromId($loc,'timezone');
			$dateTimeZone = new DateTimeZone($timezone);
			$datestr = sprintf("%02d", $date[1]) . "/" . sprintf("%02d", $date[2]) . "/" . $date[0];
			$dateTime = new DateTime($datestr, $dateTimeZone);
			// Geeft tijdsverschil terug in seconden
			$timedifference = $dateTimeZone->getOffset($dateTime);
			$timedifference = $timedifference / 3600.0;
			if ($time < 0)
				return $time;
			$time = sscanf(sprintf("%04d", $time), "%2d%2d");
			$hours = $time[0] + (int) $timedifference;
			$minutes = $time[1];
			// We are converting from UT to local time -> we should add the time difference!
			$timedifferenceminutes = ($timedifference - (int) $timedifference) * 60;
			$minutes = $minutes + $timedifferenceminutes;
			if ($minutes < 0) {
				$hours = $hours -1;
				$minutes = $minutes +60;
			} else
				if ($minutes > 60) {
					$hours = $hours +1;
					$minutes = $minutes -60;
				}
			if ($hours < 0)
				$hours = $hours +24;
			if ($hours >= 24)
				$hours = $hours -24;
			$time = $hours * 100 + $minutes;
			return $time;
		} else
			throw new Exception("Error in getDsObservationLocalTime of observations.php");
	}
	public  function getDsObservationProperty($id, $property, $defaultvalue='')                                                                                         // returns the property of the observation
	{ global $objDatabase;
		return $objDatabase->selectSingleValue("SELECT ".$property." FROM observations WHERE id=\"".$id."\"",$property,$defaultvalue);
	}
	public  function getDsObservationsCountFromObserver($id)
	{ global $objDatabase;
		return $objDatabase->selectSingleValue("SELECT COUNT(*) as Cnt FROM observations WHERE observations.observerid = \"$id\" and visibility != 7 ", "Cnt", 0);
	}
	public  function getLOObservationId($objectname, $userid, $notobservation) 
	{ global $objDatabase;
		return $objDatabase->selectSingleValue("SELECT id FROM observations WHERE objectname=\"".$objectname."\" and observerid=\"".$userid."\" and id!=\"".$notobservation."\" ORDER BY date DESC", 'id', 0);
	}
	public  function getMaxObservation()
	{ global $objDatabase; 
	  return $objDatabase->selectSingleValue('SELECT MAX(observations.id) as MaxCnt FROM observations','MaxCnt',0);
	}
	public  function getMOObservationsId($object, $userid, $notobservation) 
	{ global $objDatabase;
		return $objDatabase->selectSingleArray("SELECT observations.id FROM observations WHERE objectname=\"".$object."\" and observerid=\"".$userid."\" AND id!=\"".$notobservation."\" ORDER BY id DESC",'id');
	}
	public  function getNumberOfDifferentObservedDSObjects() 	                                                                                                          // Returns the number of different objects observed
	{ global $objDatabase;
	  return $objDatabase->selectSingleValue("SELECT COUNT(DISTINCT objectname) As Cnt FROM observations WHERE visibility != 7 ",'Cnt');
	}
	public  function getNumberOfDsObservations()                                                                                                                        // returns the total number of observations
	{ global $objDatabase;
		return $objDatabase->selectSingleValue("SELECT COUNT(objectname) As Cnt FROM observations WHERE visibility != 7 ",'Cnt',0);
	} 
	public  function getNumberOfObjects($id)                                                                                                                            // return the number of different objects seen by the observer
	{ global $objDatabase;
		return $objDatabase->selectSingleValue("SELECT COUNT(DISTINCT objectname) As Cnt FROM observations WHERE observerid=\"".$id."\" AND visibility != 7 ", 'Cnt', 0);
	}
	public  function getObjectsFromObservations($observations,$showPartOfs=0) 
	{ global $objObject;
	  $objects=array ();
		$i=0;
		while(list($key,$observation)=each($observations))
			if (!array_key_exists($observation['objectname'], $objects))
				$objects[$observation['objectname']] = array ($i++, $observation['objectname']);
		if($showPartOfs)
		  $objects=$objObject->getPartOfs($objects);
	  return $objects;
	}
	public  function getObservationFromQuery($queries, $seenpar = "D", $exactinstrumentlocation = "0")                                                                  // returns an array with the names of all observations where the queries are defined in an array. 
	{	// An example of an array :
  	//  $q = array("object" => "NGC 7293", "observer" => "wim",
  	// 		"instrument" => "3", "location" => "24",
  	//		"mindate" => "20040512", "maxdate" => "20040922",
  	//             "mindiameter" => "100", "maxdiameter" => "200", "type" => "GALXY", "con" => "AND",
  	//             "minmag" => "6.0", "maxmag" => "14.0", "minsubr" => "13.0",
  	//             "maxsubr" => "14.0", "minra" => "0.3", "maxra" => "0.9",
  	//             "mindecl" => "24.0", "maxdecl" => "30.0", "urano" => "111",
  	//             "uranonew" => "111", "sky" => "11", "msa" => "222",
  	//             "mindiam1" => "12.2", "maxdiam1" => "13.2", "mindiam2" => "11.1",
  	//             "maxdiam2" => "22.2", "description" => "Doughnut", "minvisibility" => "5",
  	//		          "maxvisibility" => "3", "minseeing" => "2", "maxseeing" => "4",
  	//             "minlimmag" => "5.5", "maxlimmag" => "6.0", $languages =>  Array ( [0] => en )),
  	//             "eyepiece" => "4", "filter" => "2", "lens" => "3", "minSmallDiameter" => "3.4",
   	//             "maxSmallDiameter" => "3.7", "minLargeDiameter" => "5.3", "maxLargeDiameter" => "6.5",
	  //             "stellar" => "1", "extended" => "0", "resolved" => "0", "mottled" => "1",
  	//             "characterType" => "A", "unusualShape" => "0", "partlyUnresolved" => "1", 
  	//             "colorContrasts" => "0", "minSQM" => "18.9", "maxSQM" => "21.2";
		$object = "";
		$sqland = "";
		$alternative = "";
		if (!array_key_exists('countquery', $queries))
			$sql1 = "SELECT DISTINCT observations.id as observationid, 
									                       observations.objectname as objectname,
												  							 observations.date as observationdate,
																				 observations.description as observationdescription, 
						  													 observers.id as observerid,
																				 CONCAT(observers.firstname , ' ' , observers.name) as observername,
						  													 CONCAT(observers.name , ' ' , observers.firstname) as observersortname,
																				 objects.con as objectconstellation, 
																			   instruments.id as instrumentid,
																				 instruments.name as instrumentname,
																				 instruments.diameter as instrumentdiameter,
						  													 CONCAT(10000+instruments.diameter,' mm ',instruments.name) as instrumentsort
																				 ";
		else
			$sql1 = "SELECT count(DISTINCT observations.id) as ObsCnt ";
		$sql2 = $sql1;
		$sql1 .= "FROM observations " .
		"JOIN instruments on observations.instrumentid=instruments.id " .
		"JOIN objects on observations.objectname=objects.name " .
		"JOIN locations on observations.locationid=locations.id " .
		"JOIN objectnames on observations.objectname=objectnames.objectname " .
		"JOIN observers on observations.observerid=observers.id ";
		$sql2 .= "FROM observations " .
		"JOIN objectpartof on objectpartof.objectname=observations.objectname " .
		"JOIN instruments on observations.instrumentid=instruments.id " .
		"JOIN objects on observations.objectname=objects.name " .
		"JOIN locations on observations.locationid=locations.id " .
		"JOIN objectnames on objectpartof.partofname=objectnames.objectname " .
		"JOIN observers on observations.observerid=observers.id ";
		if (array_key_exists('object', $queries) && ($queries["object"] != ""))
			$sqland .= "AND (objectnames.altname like \"" .$queries["object"] . "\") ";
//      $sqland .= " AND (CONCAT(UPPER(objectnames.catalog),UPPER(objectnames.catindex)) like \"" . strtoupper(str_replace(' ','',$queries["object"])) . "\") ";
		elseif (array_key_exists('catalog', $queries) && $queries["catalog"] && $queries['catalog'] != '%') 
		  $sqland .= "AND (objectnames.altname like \"" .trim($queries["catalog"] . ' ' . $queries['number'] . '%') . "\") ";
		elseif (array_key_exists('number', $queries)&&$queries['number']) 
		  $sqland .= "AND (objectnames.altname like \"" .trim($queries["number"]) . "\") ";
		$sqland .= (isset ($queries["observer"]) && $queries["observer"]) ? " AND observations.observerid = \"" . $queries["observer"] . "\" " : '';
		if (isset ($queries["instrument"]) && ($queries["instrument"] != "")) {
			$sqland .= "AND (observations.instrumentid = \"" . $queries["instrument"] . "\" ";
			if (!$exactinstrumentlocation) {
				$insts = $GLOBALS['objInstrument']->getAllInstrumentsIds($queries["instrument"]);
				while (list ($key, $value) = each($insts))
					$sqland .= " || observations.instrumentid = \"" . $value . "\" ";
			}
			$sqland .= ") ";
		}
		if (isset ($queries["eyepiece"]) && ($queries["eyepiece"] != "")) {
			$sqland .= "AND (observations.eyepieceid = \"" . $queries["eyepiece"] . "\" ";
			if (!$exactinstrumentlocation) {
				$eyeps = $GLOBALS['objEyepiece']->getAllEyepiecesIds($queries["eyepiece"]);
				while (list ($key, $value) = each($eyeps))
					$sqland .= " || observations.eyepieceid = \"" . $value . "\" ";
			}
			$sqland .= ") ";
		}
		if (isset ($queries["filter"]) && ($queries["filter"] != "")) {
			$sqland .= " AND (observations.filterid = \"" . $queries["filter"] . "\" ";
			if (!$exactinstrumentlocation) {
				$filts = $GLOBALS['objFilter']->getAllFiltersIds($queries["filter"]);
				while (list ($key, $value) = each($filts))
					$sqland .= " || observations.filterid = \"" . $value . "\" ";
			}
			$sqland .= ") ";
		}
		if (isset ($queries["lens"]) && ($queries["lens"] != "")) {
			$sqland .= "AND (observations.lensid = \"" . $queries["lens"] . "\" ";
			if (!$exactinstrumentlocation) {
				$lns = $GLOBALS['objLens']->getAllLensesIds($queries["lens"]);
				while (list ($key, $value) = each($lns))
					$sqland .= " || observations.lensid = \"" . $value . "\" ";
			}
			$sqland .= ") ";
		}
		if (isset ($queries["location"]) && ($queries["location"] != "")) {
			$sqland .= "AND (observations.locationid = \"" . $queries["location"] . "\" ";
			if (!$exactinstrumentlocation) {
				$locs = $GLOBALS['objLocation']->getAllLocationsIds($queries["location"]);
				while (list ($key, $value) = each($locs))
					if ($value != $queries["location"])
						$sqland .= " || observations.locationid = \"" . $value . "\" ";
			}
			$sqland .= ") ";
		}
		if (isset ($queries["maxdate"]) && ($queries["maxdate"] != ""))
			if (strlen($queries["maxdate"]) > 4)
				$sqland .= "AND observations.date <= \"" .
				$queries["maxdate"] . "\" ";
			else
				$sqland .= "AND RIGHT(observations.date,4) <= \"" .
				$queries["maxdate"] . "\" ";
		if (isset ($queries["mindate"]) && ($queries["mindate"] != ""))
			if (strlen($queries["mindate"]) > 4)
				$sqland .= "AND observations.date >= \"" .
				$queries["mindate"] . "\" ";
			else
				$sqland .= "AND RIGHT(observations.date,4) >= \"" .
				$queries["mindate"] . "\" ";
		$sqland .= (isset ($queries["description"]) && $queries["description"]) ? "AND observations.description like \"%" . $queries["description"] . "%\" " : '';
		$sqland .= (isset ($queries["mindiameter"]) && $queries["mindiameter"]) ? "AND instruments.diameter >= \"" . $queries["mindiameter"] . "\" " : '';
		$sqland .= (isset ($queries["maxdiameter"]) && $queries["maxdiameter"]) ? "AND instruments.diameter <= \"" . $queries["maxdiameter"] . "\" " : '';
		$sqland .= (isset ($queries["type"]) && $queries["type"]) ? "AND objects.type = \"" . $queries["type"] . "\" " : '';
		$sqland .= (isset ($queries["con"]) && $queries["con"]) ? "AND objects.con = \"" . $queries["con"] . "\" " : '';
		$sqland .= (isset ($queries["minmag"]) && (strcmp($queries["minmag"], "") != 0)) ? "AND (objects.mag > \"" . $queries["minmag"] . "\" OR objects.mag like \"" . $queries["minmag"] . "\") AND (objects.mag < 99)" : '';
		if (isset ($queries["maxmag"]) && (strcmp($queries["maxmag"], "") != 0))
			$sqland .= "AND (objects.mag < \"" . $queries["maxmag"] . "\" OR objects.mag like \"" . $queries["maxmag"] . "\") ";
		if (isset ($queries["minsb"]) && (strcmp($queries["minsb"], "") != 0))
			$sqland .= "AND objects.subr >= \"" . $queries["minsb"] . "\" ";
		if (isset ($queries["maxsb"]) && (strcmp($queries["maxsb"], "") != 0))
			$sqland .= "AND objects.subr <= \"" . $queries["maxsb"] . "\" ";
		if (isset ($queries["minra"]) && (strcmp($queries["minra"], "") != 0))
			$sqland .= "AND (objects.ra >= \"" . $queries["minra"] . "\" OR objects.ra like \"" . $queries["minra"] . "\") ";
		if (isset ($queries["maxra"]) && (strcmp($queries["maxra"], "") != 0))
			$sqland .= "AND (objects.ra <= \"" . $queries["maxra"] . "\" OR objects.ra like \"" . $queries["maxra"] . "\") ";
		if (isset ($queries["mindecl"]) && (strcmp($queries["mindecl"], "") != 0))
			$sqland .= "AND objects.decl >= \"" . $queries["mindecl"] . "\" ";
		if (isset ($queries["maxdecl"]) && (strcmp($queries["maxdecl"], "") != 0))
			$sqland .= "AND objects.decl <= \"" . $queries["maxdecl"] . "\" ";
		if (isset ($queries["minLat"]) && (strcmp($queries["minLat"], "") != 0))
			$sqland .= "AND locations.latitude >= " . $queries["minLat"] . " ";
		if (isset ($queries["maxLat"]) && (strcmp($queries["maxLat"], "") != 0))
			$sqland .= "AND locations.latitude <= " . $queries["maxLat"] . " ";
		if (isset ($queries["mindiam1"]) && (strcmp($queries["mindiam1"], "") != 0))
			$sqland .= "AND (objects.diam1 > \"$diam1\" or objects.diam1 like \"" . $queries["mindiam1"] . "\") ";
		if (isset ($queries["maxdiam1"]) && (strcmp($queries["maxdiam1"], "") != 0))
			$sqland .= "AND (objects.diam1 <= \"$diam1\" or objects.diam1 like \"" . $queries["maxdiam1"] . "\") ";
		if (isset ($queries["mindiam2"]) && (strcmp($queries["mindiam2"], "") != 0))
			$sqland .= "AND (objects.diam2 > \"$diam2\" or objects.diam2 like \"" . $queries["mindiam2"] . "\") ";
		if (isset ($queries["maxdiam2"]) && (strcmp($queries["maxdiam2"], "") != 0))
			$sqland .= "AND (objects.diam2 <= \"$diam2\" or objects.diam2 like \"" . $queries["mindiam2"] . "\") ";
		$sqland .= (isset ($queries["atlas"]) && $queries["atlas"] && isset ($queries["atlasPageNumber"]) && $queries["atlasPageNumber"]) ? "AND " . $queries["atlas"] . "=\"" . $queries["atlasPageNumber"] . "\" " : '';
		if (isset ($queries["minvisibility"]) && ($queries["minvisibility"] != ""))
			$sqland .= "AND observations.visibility <= \"" . $queries["minvisibility"] . "\" AND observations.visibility >= \"1\" ";
		if (isset ($queries["maxvisibility"]) && ($queries["maxvisibility"] != ""))
			$sqland .= "AND observations.visibility >= \"" . $queries["maxvisibility"] . "\" ";
		if (isset ($queries["minseeing"]) && ($queries["minseeing"] != ""))
			$sqland .= "AND observations.seeing <= \"" . $queries["minseeing"] . "\" ";
		if (isset ($queries["maxseeing"]) && ($queries["maxseeing"] != ""))
			$sqland .= "AND observations.seeing >= \"" . $queries["maxseeing"] . "\" ";
		if (isset ($queries["minlimmag"]) && ($queries["minlimmag"] != ""))
			$sqland .= "AND observations.limmag >= \"" . $queries["minlimmag"] . "\" ";
		if (isset ($queries["maxlimmag"]) && ($queries["maxlimmag"] != ""))
			$sqland .= "AND observations.limmag <= \"" . $queries["maxlimmag"] . "\" ";
		if (isset ($queries["minSmallDiameter"]) && ($queries["minSmallDiameter"] != ""))
			$sqland .= "AND observations.smallDiameter >= \"" . $queries["smallDiameter"] . "\" ";
		if (isset ($queries["maxSmallDiameter"]) && ($queries["maxSmallDiameter"] != ""))
			$sqland .= "AND observations.smallDiameter <= \"" . $queries["smallDiameter"] . "\" ";
		if (isset ($queries["minLargeDiameter"]) && ($queries["minLargeDiameter"] != ""))
			$sqland .= "AND observations.largeDiameter >= \"" . $queries["largeDiameter"] . "\" ";
		if (isset ($queries["maxLargeDiameter"]) && ($queries["maxLargeDiameter"] != ""))
			$sqland .= "AND observations.largeDiameter <= \"" . $queries["largeDiameter"] . "\" ";
		if (isset ($queries["stellar"]) && ($queries["stellar"] != ""))
			$sqland .= "AND observations.stellar = \"" . $queries["stellar"] . "\" ";
		if (isset ($queries["extended"]) && ($queries["extended"] != ""))
			$sqland .= "AND observations.extended = \"" . $queries["extended"] . "\" ";
		if (isset ($queries["resolved"]) && ($queries["resolved"] != ""))
			$sqland .= "AND observations.resolved = \"" . $queries["resolved"] . "\" ";
		if (isset ($queries["mottled"]) && ($queries["mottled"] != ""))
			$sqland .= "AND observations.mottled = \"" . $queries["mottled"] . "\" ";
		if (isset ($queries["characterType"]) && ($queries["characterType"] != ""))
			$sqland .= "AND observations.characterType = \"" . $queries["characterType"] . "\" ";
		if (isset ($queries["unusualShape"]) && ($queries["unusualShape"] != ""))
			$sqland .= "AND observations.unusualShape = \"" . $queries["unusualShape"] . "\" ";
		if (isset ($queries["partlyUnresolved"]) && ($queries["partlyUnresolved"] != ""))
			$sqland .= "AND observations.partlyUnresolved = \"" . $queries["partlyUnresolved"] . "\" ";
		if (isset ($queries["colorContrasts"]) && ($queries["colorContrasts"] != ""))
			$sqland .= "AND observations.colorContrasts = \"" . $queries["colorContrasts"] . "\" ";
		if (isset ($queries["minSQM"]) && ($queries["minSQM"] != ""))
			$sqland .= "AND observations.SQM >= \"" . $queries["minSQM"] . "\" ";
		if (isset ($queries["maxSQM"]) && ($queries["maxSQM"] != ""))
			$sqland .= "AND observations.SQM <= \"" . $queries["minSQM"] . "\" ";
		if (isset ($queries["hasDrawing"]) && ($queries["hasDrawing"]=='on'))
			$sqland .= "AND observations.hasDrawing=TRUE ";
			if ((!array_key_exists('countquery', $queries))
		&& (isset($queries["languages"]))) 
		{ $extra2="";
			for($i=0;$i<count($queries["languages"]);$i++)
				$extra2.="OR observations.language=\"".$queries["languages"][$i]."\" ";
			if($extra2)
				$sqland.=" AND (".substr($extra2,3).") ";
		}
		$sql = "(" . $sql1;
		if ($sqland)
			$sql .= " WHERE " . substr($sqland, 4);
		if (array_key_exists('object', $queries) && ($queries["object"] != "") && (!array_key_exists('countquery', $queries))) {
			$sql .= ") UNION (" . $sql2;
			if ($sqland)
				$sql .= " WHERE " . substr($sqland, 4);
		}
		$sql .= ")";
		if (!array_key_exists('countquery', $queries))
			$sql .= " ORDER BY observationid DESC";
		$sql = $sql . ";";
// echo $sql.'<p>'; //=========================================================== HANDY DEBUG LINE
		$run = mysql_query($sql) or die(mysql_error());
		if (!array_key_exists('countquery', $queries)) {
			$j = 0;
			$result = array ();
			while ($get = mysql_fetch_object($run)) {
				$seentype = "X";
				if (array_key_exists('deepskylog_id', $_SESSION) && ($seenpar != "D"))
					if ($GLOBALS['objDatabase']->SelectSingleValue("SELECT observations.id FROM observations WHERE objectname = \"" . $get->objectname . "\" AND observerid = \"" . $_SESSION['deepskylog_id'] . "\"", 'id')) // object has been seen by the observer logged in
						$seentype = "Y";
				if (($seenpar == "D") || ($seenpar == $seentype)) {
					while (list ($key, $value) = each($get))
						$result[$j][$key] = $value;
					$j++;
				}
			}
			return $result;
		} else {
			$get = mysql_fetch_object($run);
			return $get->ObsCnt;
		}
	}
	public  function getObservationsLastYear($id) 
	{ global $objDatabase;
	  $t=getdate();
		return $objDatabase->selectSingleValue("SELECT COUNT(*) AS Cnt FROM observations WHERE observations.observerid LIKE \"".$id."\" AND observations.date > \"" . date('Ymd', strtotime('-1 year'))."\""/* AND observations.visibility != 7 */, 'Cnt', 0);
	}
	public  function getObservationsUserObject($userid, $object) 
	{ global $objDatabase;
		return $objDatabase->selectSingleValue("SELECT COUNT(*) As ObsCnt FROM observations WHERE observerid=\"" . $userid . "\" AND observations.objectname=\"" . $object . "\"", "ObsCnt");
	}
	public  function getObservedCountFromCatalogOrList($id, $catalog) 
	{ global $objDatabase;
		if (substr($catalog, 0, 5) == 'List:') 
			if (substr($catalog, 5, 7) == "Public:")
				$sql = "SELECT COUNT(DISTINCT observations.objectname) AS CatCnt " .
					     "FROM observations " .
					     "JOIN observerobjectlist on observerobjectlist.objectname=observations.objectname " .
					     "JOIN observers on observations.observerid = observers.id " .
					     "WHERE observerobjectlist.listname=\"" . substr($catalog, 5) . "\" " .
			         "AND observations.observerid=\"".$id."\" " .
				       "AND observations.visibility != 7 ";
			else
			  $sql = "SELECT COUNT(DISTINCT observations.objectname) AS CatCnt " .
					     "FROM observations " .
					     "JOIN observerobjectlist on observerobjectlist.objectname=observations.objectname " .
					     "JOIN observers on observations.observerid = observers.id " .
					     "WHERE observerobjectlist.listname=\"" . substr($catalog, 5) . "\" " .
					     "AND observerobjectlist.observerid = \"" . $_SESSION['deepskylog_id'] . "\" " .
			         "AND observations.observerid=\"".$id."\" " .
			         "AND observations.visibility != 7 "; 
		else 
	    $sql = "SELECT COUNT(DISTINCT objectnames.catindex) AS CatCnt FROM objectnames " .
			       "INNER JOIN observations ON observations.objectname = objectnames.objectname " .
			       "WHERE objectnames.catalog = \"".$catalog."\" " .
			       "AND observations.observerid=\"".$id."\" " .
			       "AND observations.visibility != 7 ";
		return $objDatabase->selectSingleValue($sql,'CatCnt',0);
	}
	public  function getObservedFromCatalog($id, $catalog) 
	{ global $objDatabase;
	  if (substr($catalog, 0, 5) == "List:")
			if (substr($catalog, 5, 7) == "Public:")
				$sql = "SELECT DISTINCT observerobjectlist.objectname FROM observerobjectlist " .
				"INNER JOIN observations ON observations.objectname = observerobjectlist.objectname " .
				"WHERE ((observerobjectlist.listname = \"" . substr($catalog, 5) . "\") " .
				"AND (observations.observerid = \"" . $id . "\") " .
				"AND (observations.visibility != 7))";
			else
				$sql = "SELECT DISTINCT observerobjectlist.objectname FROM observerobjectlist " .
				"INNER JOIN observations ON observations.objectname = observerobjectlist.objectname " .
				"WHERE ((observerobjectlist.listname = \"" . substr($catalog, 5) . "\") AND (observerobjectlist.observerid = \"" . $_SESSION['deepskylog_id'] . "\") " .
				"AND (observations.observerid = \"" . $id . "\") " .
				"AND (observations.visibility != 7))";
		else
			$sql = "SELECT DISTINCT objectnames.objectname FROM objectnames " .
			"INNER JOIN observations ON observations.objectname = objectnames.objectname " .
			"WHERE ((objectnames.catalog = \"$catalog\") " .
			"AND (observations.observerid=\"$id\") " .
			"AND (observations.visibility != 7))";
		return $objDatabase->selectSingleArray($sql, 'objectname');
	}
	public  function getObservedFromCatalogPartOf($id, $catalog) 
  { global $objDatabase;
  	if (substr($catalog, 0, 5) == "List:")
			if (substr($catalog, 5, 7) == "Public:")
				$sql = "SELECT DISTINCT observerobjectlist.objectname FROM observerobjectlist " .
				      " JOIN objectpartof ON objectpartof.partofname = observerobjectlist.objectname " .
				      " JOIN observations ON observations.objectname = objectpartof.objectname " .
						  " WHERE ((observerobjectlist.listname = \"" . substr($catalog, 5) . "\") " .
				      " AND (observations.observerid = \"" . $id . "\") " .
				      " AND (observations.visibility != 7))";
			else
				$sql = "SELECT DISTINCT observerobjectlist.objectname FROM observerobjectlist " .
				      " JOIN objectpartof ON objectpartof.partofname = observerobjectlist.objectname " .
				      " JOIN observations ON observations.objectname = objectpartof.objectname " .
				      " WHERE ((observerobjectlist.listname = \"" . substr($catalog, 5) . "\") AND (observerobjectlist.observerid = \"" . $_SESSION['deepskylog_id'] . "\") " .
				      " AND (observations.observerid = \"" . $id . "\") " .
				      " AND (observations.visibility != 7))";
		else
			$sql = "SELECT DISTINCT objectnames.objectname FROM objectnames " .
			      " JOIN objectpartof ON objectpartof.partofname = objectnames.objectname " .
			      " JOIN observations ON observations.objectname = objectpartof.objectname " .
			      " WHERE ((objectnames.catalog = \"$catalog\") " .
			      " AND (observations.observerid=\"$id\") " .
			      " AND (observations.visibility != 7))";
		return $objDatabase->selectSingleArray($sql, 'objectname');
	}
	public  function getPopularObservations()                                                                                                                           // returns the number of observations of the objects
	{ global $objDatabase;
		$run = $objDatabase->selectRecordset("SELECT observations.objectname, COUNT(observations.id) As ObservationCount FROM observations GROUP BY observations.objectname ORDER BY ObservationCount DESC");
		$i=1;
		while($get=mysql_fetch_object($run))
			$numberOfObservations[$get->objectname]=array($i++,$get->objectname);
		return $numberOfObservations;
	}
	public  function getPopularObservers()                                    //  returns the number of observations of the observers
	{ global $objDatabase;
	  return $objDatabase->selectSingleArray("SELECT observations.observerid, COUNT(observations.id) As Cnt FROM observations GROUP BY observations.observerid ORDER BY Cnt DESC", 'observerid');
	}
	public  function getPopularObserversOverviewCatOrList($sort, $cat = "") 
	{ global $objDatabase;
	  if ($sort == "jaar") {
			$t = getdate();
			$sql = "SELECT observations.observerid, COUNT(*) AS Cnt, observers.name " .
			       "FROM observations " .
			       "JOIN observers on observations.observerid = observers.id " .
			       "WHERE observations.date > \"" . date('Ymd', strtotime('-1 year')) . "\" AND observations.visibility != \"7\" ";
		}
		elseif ($sort == "catalog") {
			if (substr($cat, 0, 5) == "List:")
				if (substr($cat, 5, 7) == "Public:")
					$sql = "SELECT observations.observerid, COUNT(DISTINCT observations.objectname) AS Cnt, observers.name " .
					"FROM observations " .
					"JOIN observerobjectlist on observerobjectlist.objectname=observations.objectname " .
					"JOIN observers on observations.observerid = observers.id " .
					"WHERE observerobjectlist.listname=\"" . substr($cat, 5) . "\" " .
					"AND observations.visibility != 7 ";
				else
					$sql = "SELECT observations.observerid, COUNT(DISTINCT observations.objectname) AS Cnt, observers.name " .
					"FROM observations " .
					"JOIN observerobjectlist on observerobjectlist.objectname=observations.objectname " .
					"JOIN observers on observations.observerid = observers.id " .
					"WHERE observerobjectlist.listname=\"" . substr($cat, 5) . "\" " .
					"AND observerobjectlist.observerid = \"" . $_SESSION['deepskylog_id'] . "\" " .
					"AND observations.visibility != 7 ";
			else
				$sql = "SELECT observations.observerid, COUNT(DISTINCT objectnames.catindex) AS Cnt, observers.name " .
				"FROM observations " .
				"JOIN objectnames on observations.objectname=objectnames.objectname " .
				"JOIN observers on observations.observerid = observers.id " .
				"WHERE objectnames.catalog=\"$cat\" AND observations.visibility != 7 ";
		}
		elseif ($sort == "objecten") {
			$sql = "SELECT observations.observerid, COUNT(DISTINCT observations.objectname) AS Cnt " .
			"FROM observations " .
			"JOIN observers on observations.observerid = observers.id WHERE observations.visibility != 7 ";
		} else {
			$sql = "SELECT observations.observerid, COUNT(*) AS Cnt " .
			"FROM observations " .
			"JOIN observers on observations.observerid = observers.id WHERE observations.visibility != 7 ";
		}
		$sql .= "GROUP BY observations.observerid, observers.name ";
		if ($sort == "observer")
			$sql .= "ORDER BY observers.name ASC ";
		else
			$sql .= "ORDER BY Cnt DESC, observers.name ASC ";
		return $objDatabase->selectKeyValueArray($sql, 'observerid', 'Cnt');
	}
  public  function setDsObservationProperty($id,$property,$propertyValue)                                                                                                       // sets the property to the specified value for the given observation
  { global $objDatabase;
    return $objDatabase->execSQL("UPDATE observations SET ".$property." = \"".$propertyValue."\" WHERE id = \"".$id."\"");
  }
	public  function setLocalDateAndTime($id, $date, $time) 	                                                                                                                     // sets the date and time for the given observation when the time is given in  local time
	{ global $objDatabase,$objLocation;
		if ($time >= 0) 
		{ $objDatabase->selectSingleValue("SELECT locationid FROM observations WHERE id = \"$id\"");
			$timezone = $objLocation->getLocationPropertyFromId($this->getDsObservationProperty($id,'locationid'),'timezone');
			$datearray = sscanf($date, "%4d%2d%2d");
			$dateTimeZone = new DateTimeZone($timezone);
			$date = sprintf("%02d", $datearray[1]) . "/" . sprintf("%02d", $datearray[2]) . "/" . $datearray[0];
			$dateTime = new DateTime($date, $dateTimeZone);
			// Returns the timedifference in seconds
			$timedifference = $dateTimeZone->getOffset($dateTime);
			$timedifference = $timedifference / 3600.0;
			$timestr = sscanf(sprintf("%04d", $time), "%2d%2d");
			$jd = cal_to_jd(CAL_GREGORIAN, $datearray[1], $datearray[2], $datearray[0]);
			$hours = $timestr[0] - (int) $timedifference;
			$timedifferenceminutes = ($timedifference - (int) $timedifference) * 60;
			$minutes = $timestr[1] - $timedifferenceminutes;
			if ($minutes < 0) {
				$hours = $hours -1;
				$minutes = $minutes +60;
			} else
				if ($minutes > 60) {
					$hours = $hours +1;
					$minutes = $minutes -60;
				}
			if ($hours < 0) {
				$hours = $hours +24;
				$jd = $jd -1;
			}
			if ($hours >= 24) {
				$hours = $hours -24;
				$jd = $jd +1;
			}
			$time = $hours * 100 + $minutes;
			$dte = JDToGregorian($jd);
			sscanf($dte, "%2d/%2d/%4d", $month, $day, $year);
			$date = $year . sprintf("%02d", $month) . sprintf("%02d", $day);
		}
		$objDatabase->execSQL("UPDATE observations SET date = \"".$date."\" WHERE id = \"".$id."\"");
		$objDatabase->execSQL("UPDATE observations SET time = \"$time\" WHERE id = \"".$id."\"");
	}
  public  function validateDeleteDSObservation($id)                                                                                                                   // removes the observation with id = $id
	{ global $objDatabase,$objUtil;
	  if(!$_GET['observationid'])
      throw new Exception("No observation to delete.");                           
    if(($id=$objUtil->checkGetKey('observationid'))
    && ($objUtil->checkAdminOrUserID($this->getDsObservationProperty($id,'observerid'))))
    { $objDatabase->execSQL("DELETE FROM observations WHERE id=\"".$id."\"");
	    $_SESSION['Qobs']=array();
	    $_SESSION['QobsParams']=array();
      return LangObservationDeleted;
    }
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	


	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	





	












	public  function showObservation($LOid) 
	{ global $objUtil, $dateformat, $myList, $listname_ss, $baseURL, $objEyepiece, $objObserver, $objInstrument, $loggedUser, $objObject;
		$link=$baseURL."index.php?";
		$linkamp="";
		reset($_GET);
		while(list($key,$value)=each($_GET))
		  $linkamp.=$key."=".urlencode($value)."&amp;";
		$inst=$objInstrument->getInstrumentPropertyFromId($this->getDsObservationProperty($LOid,'instrumentid'),'name');
		if($inst=="Naked eye")
		 	$inst=InstrumentsNakedEye;
		$dateTimeText="";
		$date=sscanf($this->getDsObservationProperty($LOid,'date'),"%4d%2d%2d");
		$time="";
		$dateTimeLabelText="";
		$dateTimeText=date($dateformat, mktime(0, 0, 0, $date[1], $date[2], $date[0]));
		if($this->getDsObservationProperty($LOid,'time')>0) 
			if($loggedUser&&$objObserver->getUseLocal($loggedUser))
	  	{ $date=sscanf($this->getDsObservationLocalDate($LOid),"%4d%2d%2d");
		  	$dateTimeLabelText="&nbsp;" . LangViewObservationField9lt;
			  $time=$this->getDsObservationLocalTime($LOid);
		  } 
		  else 
		  { $dateTimeLabelText="&nbsp;".LangViewObservationField9;
		  	$time=$this->getDsObservationProperty($LOid,'time');
		  }
		if($time) 
		{ $time=sscanf(sprintf("%04d",$time),"%2d%2d");
			$dateTimeText.="&nbsp;".$time[0].":".sprintf("%02d",$time[1]);
		}
		$seeing = $this->getDsObservationProperty($LOid,'seeing');
		$diameterText='';
		if($largeDiameter=$this->getDsObservationProperty($LOid,'largeDiameter'))
		{ if($largeDiameter>60)
			{ $diameterText=sprintf("%.1f",$largeDiameter/60.0);
				if($smallDiameter=$this->getDsObservationProperty($LOid,'smalldiameter'))
				  $diameterText.=sprintf(" x %.1f",$smallDiameter/60.0);
			}
			else 
			{ $diameterText=sprintf("%.1f",$largeDiameter);			
				if($smallDiameter) 
				  $diameterText=sprintf(" x %.1f",$smallDiameter);
			}	
		} 
		else
		  $diameterText="-";
		$details1Text="";
		if($this->getDsObservationProperty($LOid,'stellar')>0)  $details1Text.=", ".LangViewObservationField35;
    if($this->getDsObservationProperty($LOid,'extended')>0) $details1Text.=", ".LangViewObservationField36;
    if($this->getDsObservationProperty($LOid,'resolved')>0) $details1Text.=", ".LangViewObservationField37;
    if($this->getDsObservationProperty($LOid,'mottled')>0)  $details1Text.=", ".LangViewObservationField38;
    $details1Text=substr($details1Text,2);
		$details2Text="";
		if($this->getDsObservationProperty($LOid,'unusualShape')>0) $details2Text.=", ".LangViewObservationField41;
		if($this->getDsObservationProperty($LOid,'partlyUnresolved')>0) $details2Text.=", ".LangViewObservationField42;
		if($this->getDsObservationProperty($LOid,'colorContrasts')>0) $details2Text.=", ".LangViewObservationField43;
		$details2Text=substr($details2Text,2);
		$charTypeText="";
		if(in_array($objObject->getDsoProperty($this->getDsObservationProperty($LOid,'objectname'),'type'),array("ASTER","CLANB","DS","OPNCL","AA1STAR","AA2STAR","AA3STAR","AA4STAR","AA8STAR","GLOCL")))
		  $charTypeText=(($characterType=$this->getDsObservationProperty($LOid,'characterType'))?$characterType.': '.$GLOBALS['ClusterType'.$characterType]:"-");
		echo "<table width=\"100%\">";
		tableFieldnameField3(LangViewObservationField2,
		                     "<a href=\"".$baseURL."index.php?indexAction=detail_observer&amp;user=".urlencode($this->getDsObservationProperty($LOid,'observerid'))."&amp;back=index.php?indexAction=detail_observation\">".$objObserver->getFirstName($this->getDsObservationProperty($LOid,'observerid'))."&nbsp;".$objObserver->getObserverName($this->getDsObservationProperty($LOid,'observerid'))."</a>",
		                     LangViewObservationField3,
		                     "<a href=\"".$baseURL."index.php?indexAction=detail_instrument&amp;instrument=".urlencode($this->getDsObservationProperty($LOid,'instrumentid'))."\">".$inst."</a>",
		                     LangViewObservationField4,
                         "<a href=\"".$baseURL."index.php?indexAction=detail_location&amp;location=" . urlencode($this->getDsObservationProperty($LOid,'locationid')) . "\">" . $GLOBALS['objLocation']->getLocationPropertyFromId($this->getDsObservationProperty($LOid,'locationid'),'name') . "</a>",
                         "class=\"type3\"");                                     
		tableFieldnameField3(LangViewObservationField5.$dateTimeLabelText,
                         $dateTimeText,
  	                     LangViewObservationField6,
		                     (($seeing)?$GLOBALS['Seeing'.$seeing]:"-"),
		                     LangViewObservationField7."/".LangViewObservationField34,
		                     (($limmag=$this->getDsObservationProperty($LOid,'limmag'))?sprintf("%1.1f", $limmag):"-")."/".((($sqm=$this->getDsObservationProperty($LOid,'SQM'))!=-1)?sprintf("%2.1f", $sqm):'-'),
		                     "class=\"type1\"");             
		tableFieldnameField3(LangViewObservationField30,
		                     (((($eyepiece=$this->getDsObservationProperty($LOid,'eyepieceid'))=="")||($eyepiece==0))?"-":"<a href=\"".$baseURL."index.php?indexAction=detail_eyepiece&amp;eyepiece=".urlencode($eyepiece)."\">" .stripslashes($objEyepiece->getEyepiecePropertyFromId($eyepiece,'name')). "</a>"),
		                     LangViewObservationField31,		
		                     (((($filter=$this->getDsObservationProperty($LOid,'filterid'))=="")||($filter==0))?"-":"<a href=\"".$baseURL."index.php?indexAction=detail_filter&amp;filter=".urlencode($filter)."\">".$objFilter->getFilterPropertyFromId($filter,'name')."</a>"),
		                     LangViewObservationField32,
		                     (((($lens=$this->getDsObservationProperty($LOid,'lensid'))=="")||($lens==0))?"-":"<a href=\"".$baseURL."index.php?indexAction=detail_lens&amp;lens=".urlencode($lens)."\">".$objLens->getLensPropertyFromId($lens,'name')."</a>"),
		                     "class=\"type2\"");             
		tableFieldnameField3(LangViewObservationField22,
		                     ((($visibility=$this->getDsObservationProperty($LOid,'visibility'))!="0")?$GLOBALS['Visibility'.$visibility]:"-"),
		                     LangViewObservationField33,
		                     $diameterText,
		                     LangViewObservationField40,
		                     $charTypeText,
		                     "class=\"type1\"");                     
		echo "</table>";
		echo $details1Text." ".$details2Text;
		echo "<p />";
		$LOdescription = preg_replace("/&amp;/", "&", $this->getDsObservationProperty($LOid,'description'));
		$patterns[0] = "/\s+(M)\s*(\d+)\s/";
		$replacements[0] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=M%20\\2\">&nbsp;M&nbsp;\\2&nbsp;</a>";
		$patterns[1] = "/(NGC|Ngc|ngc)\s*(\d+\w+)/";
		$replacements[1] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=NGC%20\\2\">NGC&nbsp;\\2</a>";
		$patterns[2] = "/(IC|Ic|ic)\s*(\d+)/";
		$replacements[2] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=IC%20\\2\">IC&nbsp;\\2</a>";
		$patterns[3] = "/(Arp|ARP|arp)\s*(\d+)/";
		$replacements[3] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=Arp%20\\2\">Arp&nbsp;\\2</a>";
		echo preg_replace($patterns, $replacements, $LOdescription);
		if($this->getDsObservationProperty($LOid,'hasDrawing'))
		  echo "<p>"."<a href=\"".$baseURL."deepsky/drawings/" . $LOid . ".jpg" . "\"> <img class=\"account\" src=\"" . $baseURL . "deepsky/drawings/" . $LOid . ".jpg\"></img></a>";
		echo "<p>";
		echo "<a href=\"".$baseURL."index.php?indexAction=adapt_observation&amp;observation=" . $LOid . "\">" . LangChangeObservationTitle . "</a>";
		echo "<span align=\"right\"><a href=\"".$baseURL."index.php?indexAction=validate_delete_observation&amp;observationid=".$LOid."\">".LangDeleteObservation."</a></span>";
		echo "</p>";
		echo "<hr>";
		echo "<p><span=\"text-align:center\">".(($myList)?"<a href=\"".$link.$linkamp."addObservationToList=".urlencode($LOid)."\">".LangViewObservationField44.$listname_ss."</a>":'')."</span>";
	}

	function showCompactObservationLO($obsKey, $link, $myList = false) {
		global $baseURL, $loggedUser, $objObserver, $dateformat; 
		$value=$_SESSION['Qobs'][$obsKey];
		$object=$value['objectname'];
		$observer=$value['observerid'];
		$temp=$value['instrumentid'];
		$instrument=$value['instrumentname'];
		$instrumentsize=round($value['instrumentdiameter'], 0);
		$desc=$value['observationdescription'];
		$patterns[0]="/\s+(M)\s*(\d+)/";
		$replacements[0]="<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=M%20\\2\">&nbsp;M&nbsp;\\2</a>";
		$patterns[1]= "/(NGC|Ngc|ngc)\s*(\d+\w+)/";
		$replacements[1]="<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=NGC%20\\2\">NGC&nbsp;\\2</a>";
		$patterns[2]= "/(IC|Ic|ic)\s*(\d+)/";
		$replacements[2]="<a 	href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=IC%20\\2\">IC&nbsp;\\2</a>";
		$patterns[3]= "/(Arp|ARP|arp)\s*(\d+)/";
		$replacements[3]="<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=Arp%20\\2\">Arp&nbsp;\\2</a>";
		$description=preg_replace($patterns, $replacements, $desc);
		$AOid=$this->getLOObservationId($object, $_SESSION['deepskylog_id'], $value['observationid']);
		$LOid="";
		$LOdescription="";
		if($AOid) 
		{ $LOid = $AOid;
			$LOdesc = preg_replace("/&amp;/", "&", $this->getDsObservationProperty($LOid,'description'));
			$patterns[0] = "/\s+(M)\s*(\d+)/";
			$replacements[0] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=M%20\\2\">&nbsp;M&nbsp;\\2</a>";
			$patterns[1] = "/(NGC|Ngc|ngc)\s*(\d+\w+)/";
			$replacements[1] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=NGC%20\\2\">NGC&nbsp;\\2</a>";
			$patterns[2] = "/(IC|Ic|ic)\s*(\d+)/";
			$replacements[2] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=IC%20\\2\">IC&nbsp;\\2</a>";
			$patterns[3] = "/(Arp|ARP|arp)\s*(\d+)/";
			$replacements[3] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=Arp%20\\2\">Arp&nbsp;\\2</a>";
			$LOdescription = preg_replace($patterns, $replacements, $LOdesc);
		}
		if ($LOdescription) 
		{ $LOtemp = $this->getDsObservationProperty($LOid,'instrumentid');
			$LOinstrument = $GLOBALS['objInstrument']->getInstrumentPropertyFromId($LOtemp,'name');
			$LOinstrumentsize = round($GLOBALS['objInstrument']->getInstrumentPropertyFromId($LOtemp,'diameter'), 0);
		} 
		else 
		{ $LOtemp = '';
			$LOinstrument = '';
			$LOinstrumentsize = '';
			$LOdescription = '';
		}
		if ($instrument == "Naked eye")   $instrument = InstrumentsNakedEye;
		if ($LOinstrument == "Naked eye") $LOinstrument = InstrumentsNakedEye;
		if ($loggedUser&&$objObserver->getUseLocal($loggedUser))
			$date = sscanf($this->getDsObservationLocalDate($value['observationid']), "%4d%2d%2d");
		else 
			$date = sscanf($this->getDsObservationProperty($value['observationid'],'date'), "%4d%2d%2d");
		if($loggedUser&&$objObserver->getUseLocal($loggedUser))
			$LOdate = sscanf($this->getDsObservationLocalDate($LOid), "%4d%2d%2d");
		else 
			$LOdate = sscanf($this->getDsObservationProperty($LOid,'date'), "%4d%2d%2d");
		// OUTPUT
		$con=$value['objectconstellation'];
		echo "<tr class=\"type2\">";
		echo "<td><a href=\"".$baseURL."index.php?indexAction=detail_object&amp;object=" . urlencode($object) . "\">$object</a></td>";
		echo "<td>".$GLOBALS[$con]."</td>";
		echo "<td><a href=\"".$baseURL."index.php?indexAction=detail_observer&amp;user=" . urlencode($observer) . "\">" . $value['observername'] . "</a></td>";
		echo "<td><a href=\"".$baseURL."index.php?indexAction=detail_instrument&amp;instrument=" . urlencode($temp) . "\">" . $instrument . " &nbsp;".(($instrument!=InstrumentsNakedEye)?("(" . $instrumentsize . "&nbsp;mm" . ")"):"")."</a></td>";
		echo "<td>".date($dateformat, mktime(0, 0, 0, $date[1], $date[2], $date[0]));"</td>";
		echo "<td>".(($LOdescription)?("<a href=\"".$baseURL."index.php?indexAction=detail_instrument&amp;instrument=" . urlencode($LOtemp) . "\">" . $LOinstrument . " &nbsp;").(($LOinstrument != InstrumentsNakedEye)?("(" . $LOinstrumentsize . "&nbsp;mm" . ")"):""):"")."</a>"."</td>";
		echo "<td>".($LOdescription?date($dateformat, mktime(0, 0, 0, $LOdate[1], $LOdate[2], $LOdate[0])):"")."</td>";
		echo "<td>"."<a href=\"".$baseURL."index.php?indexAction=detail_observation&amp;observation=" . $value['observationid'] . "&amp;QobsKey=" . $obsKey . "&amp;dalm=D\" title=\"" . LangDetail . "\">" . LangDetailText;
		if($this->getDsObservationProperty($value['observationid'],'hasDrawing'))
			echo LangDetailDrawingText;
		echo "</a>&nbsp;";
		echo "<a href=\"".$baseURL."index.php?indexAction=detail_observation&observation=" . $value['observationid'] . "&amp;dalm=AO\" title=\"" . LangAO . "\">";
		echo LangAOText;
		echo "</a>";
		echo "&nbsp;";
		if($loggedUser)
		{ $objectid=$this->getDsObservationProperty($value['observationid'],'objectname');
			if ($LOdescription) 
			{ echo "<a href=\"".$baseURL."index.php?indexAction=detail_observation&observation=" . $value['observationid'] . "&amp;dalm=MO\" title=\"" . LangMO . "\">";
				echo LangMOText;
				echo "</a>&nbsp;";
				echo "<a href=\"".$baseURL."index.php?indexAction=detail_observation&observation=" . $value['observationid'] . "&amp;dalm=LO\" title=\"" . LangLO . "\">";
				echo LangLOText;
				echo "</a>&nbsp;";
			}
		}
		echo ("</td>");
		if($GLOBALS['myList']) 
		{ echo ("<td>");
			$listname = $_SESSION['listname'];
			$observer = $_SESSION['deepskylog_id'];
			$sql = "SELECT Count(observerobjectlist.objectname) As ObjCnt FROM observerobjectlist WHERE observerid = \"$observer\" AND objectname=\"$object\" AND listname=\"$listname\"";
			$run = mysql_query($sql) or die(mysql_error());
			$get = mysql_fetch_object($run);
			if($get->ObjCnt>0)
			{ echo "<a href=\"".$link."&amp;addObservationToList=".urlencode($value['observationid'])."\" title=\"".LangViewObservationField44."\">E</a>";
			  echo "&nbsp;-&nbsp;";
				echo "<a href=\"".$link."&amp;removeObjectFromList=".urlencode($objectid)."\" title=\"".$object.LangListQueryObjectsMessage3.$_SESSION['listname']."\">R</a>";
			}
			else
      { echo "<a href=\"".$link."&amp;addObjectToList=".urlencode($objectid)."&amp;showname=".urlencode($object)."\" title=\"".$object.LangListQueryObjectsMessage2.$_SESSION['listname']."\">L</a>";
			  echo "&nbsp;-&nbsp;";
				echo "<a href=\"".$link."&amp;addObservationToList=".urlencode($value['observationid'])."\" title=\"".LangViewObservationField44."\">E</a>";
			}
			echo ("</td>");
		}
		echo ("</tr>\n");

		echo ("<tr class=\"type1\">\n");
		echo ("<td valign=\"top\">");
		$altnames = $GLOBALS['objObject']->getAlternativeNames($object);
		$alt = "";
		while (list ($key, $altvalue) = each($altnames)) // go through names array
			{
			if (trim($altvalue) != trim($object)) {
				if ($alt)
					$alt .= "<br>" . trim($altvalue);
				else
					$alt = trim($altvalue);
			}
		}
		echo $alt;
		echo ("</td>");
		echo ("<td colspan=\"4\">");
		echo ($description . "<P>");
		echo ("</td>\n");
		echo ("<td colspan=\"3\">");
		echo ($LOdescription . "<P>");
		echo ("</td>\n");
		echo ("</tr>\n");

		echo "<tr>";
		echo "<td> &nbsp; </td>";
		echo "<td colspan=4>";
    if($this->getDsObservationProperty($value['observationid'],'hasDrawing'))
    {	echo "<p>";
			echo "<a href=\"" . $baseURL . "deepsky/drawings/" . $value['observationid'] . ".jpg" . "\"> <img class=\"account\" src=\"" . $baseURL . "deepsky/drawings/" . $value['observationid'] . "_resized.jpg\"></img></a>";
			echo "</p>";
		}
		echo "</td>";
		echo "<td colspan=3>";
		if($LOdescription&&($this->getDsObservationProperty($LOid,'hasDrawing')))
			 echo "<p>"."<a href=\"".$baseURL."deepsky/drawings/" . $LOid . ".jpg" . "\"> <img class=\"account\" src=\"".$baseURL."deepsky/drawings/".$LOid."_resized.jpg\"></img></a>"."</p>";
		echo "</td>";
		echo "</tr>";
	}

	function ShowCompactObservation($obsKey, $link, $myList = false) {
		global $dateformat, $objObserver, $baseURL;
		$value = $_SESSION['Qobs'][$obsKey];
		// OBJECT
		$object = $value['objectname'];
		// OBSERVER
		$observer = $value['observerid'];
		// INSTRUMENT
		$temp = $value['instrumentid'];
		$instrument = $value['instrumentname'];
		$instrumentsize = round($value['instrumentdiameter'], 0);
		// DESCRIPTION
		$desc = $value['observationdescription'];
		$patterns[0] = "/\s+(M)\s*(\d+)/";
		$replacements[0] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=M%20\\2\">&nbsp;M&nbsp;\\2</a>";
		$patterns[1] = "/(NGC|Ngc|ngc)\s*(\d+\w+)/";
		$replacements[1] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=NGC%20\\2\">NGC&nbsp;\\2</a>";
		$patterns[2] = "/(IC|Ic|ic)\s*(\d+)/";
		$replacements[2] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=IC%20\\2\">IC&nbsp;\\2</a>";
		$patterns[3] = "/(Arp|ARP|arp)\s*(\d+)/";
		$replacements[3] = "<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=Arp%20\\2\">Arp&nbsp;\\2</a>";
		$description = preg_replace($patterns, $replacements, $desc);
		if ($instrument == "Naked eye")
			$instrument = InstrumentsNakedEye;
		// OUTPUT
		$con = $GLOBALS['objObject']->getDsoProperty($object,'con');
		echo ("<tr class=\"type2\">\n
				         <td><a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=" . urlencode($object) . "\">$object</a></td>\n
				    <td> " . $GLOBALS[$con] . "</td>\n
				         <td><a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_observer&amp;user=" . urlencode($value['observerid']) . "\">" . $value['observername'] . "</a></td>\n
				         <td><a href=\"" . $GLOBALS['baseURL'] . "index.php?detail_instrument&amp;instrument=" . urlencode($temp) . "\">$instrument &nbsp;");
		if ($instrument != InstrumentsNakedEye)
			echo ("(" . $instrumentsize . "&nbsp;mm" . ")");
		echo ("</a></td><td>");
		// DATE
		if (array_key_exists('deepskylog_id', $_SESSION) && $_SESSION['deepskylog_id'] && $GLOBALS['objObserver']->getUseLocal($_SESSION['deepskylog_id']))
			$date = sscanf($this->getDsObservationLocalDate($value['observationid']), "%4d%2d%2d");
		else
			$date = sscanf($this->getDsObservationProperty($value['observationid'],'date'), "%4d%2d%2d");
		echo date($dateformat, mktime(0, 0, 0, $date[1], $date[2], $date[0]));
		echo ("</td>\n");
		echo ("<td>");
		echo ("<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_observation&amp;observation=" . $value['observationid'] . "&amp;QobsKey=" . $obsKey . "&amp;dalm=D\" title=\"" . LangDetail . "\">" . LangDetailText);
		if($this->getDsObservationProperty($value['observationid'],'hasDrawing'))
			echo LangDetailDrawingText;
		echo ("</a>&nbsp;");
		echo ("<a href=\"".$baseURL."index.php?indexAction=detail_observation&amp;observation=" . $value['observationid'] . "&dalm=AO\" title=\"" . LangAO . "\">");
		echo LangAOText;
		echo ("</a>&nbsp;");
		if (array_key_exists('deepskylog_id', $_SESSION) && $_SESSION['deepskylog_id']) // LOGGED IN
			{
			if ($this->getLOObservationId($object, $_SESSION['deepskylog_id'], $value['observationid'])) {
				echo ("<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_observation&amp;observation=" . $value['observationid'] . "&amp;dalm=MO\" title=\"" . LangMO . "\">");
				echo LangMOText;
				echo ("</a>&nbsp;");
				echo ("<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_observation&amp;observation=" . $value['observationid'] . "&amp;dalm=LO\" title=\"" . LangLO . "\">");
				echo LangLOText;
				echo ("</a>&nbsp;");
			}
		}
		echo ("</td>");
		if($GLOBALS['myList']) 
		{ $objectid = $this->getDsObservationProperty($value['observationid'],'objectname');
			echo ("<td>");
			$listname = $_SESSION['listname'];
			$observer = $_SESSION['deepskylog_id'];
			$sql = "SELECT Count(observerobjectlist.objectname) As ObjCnt FROM observerobjectlist WHERE observerid = \"$observer\" AND objectname=\"$object\" AND listname=\"$listname\"";
			$run = mysql_query($sql) or die(mysql_error());
			$get = mysql_fetch_object($run);
			if($get->ObjCnt>0)
			{ echo "<a href=\"".$link."&amp;addObservationToList=".urlencode($value['observationid'])."\" title=\"".LangViewObservationField44."\">E</a>";
			  echo "&nbsp;-&nbsp;";
				echo "<a href=\"".$link."&amp;removeObjectFromList=".urlencode($objectid)."\" title=\"".$object.LangListQueryObjectsMessage3.$_SESSION['listname']."\">R</a>";
			}
			else
      { echo "<a href=\"".$link."&amp;addObjectToList=".urlencode($objectid)."&amp;showname=".urlencode($object)."\" title=\"".$object.LangListQueryObjectsMessage2.$_SESSION['listname']."\">L</a>";
			  echo "&nbsp;-&nbsp;";
				echo "<a href=\"".$link."&amp;addObservationToList=".urlencode($value['observationid'])."\" title=\"".LangViewObservationField44."\">E</a>";
			}
			echo ("</td>");
		}
		echo ("</tr>\n");
		echo ("<tr class=\"type1\">\n");
		echo ("<td valign=\"top\">");
		$altnames = $GLOBALS['objObject']->getAlternativeNames($object);
		$alt = "";
		while (list ($key, $altvalue) = each($altnames)) // go through names array
			{
			if (trim($altvalue) != trim($object)) {
				if ($alt)
					$alt .= "<br>" . trim($altvalue);
				else
					$alt = trim($altvalue);
			}
		}
		echo $alt;
		echo ("</td>");
		echo ("<td colspan=\"5\">");
		echo ($description . "<P>");
		echo ("</td>\n");
		echo ("</tr>\n");
		echo "<tr>";
		echo "<td>";
		echo "</td>";
		echo "<td colspan=6>";
		if($this->getDsObservationProperty($value['observationid'],'hasDrawing'))
			echo "<p>"."<a href=\"".$baseURL."deepsky/drawings/" . $value['observationid'] . ".jpg" . "\"> <img class=\"account\" src=\"".$baseURL."deepsky/drawings/".$value['observationid']."_resized.jpg\"></img></a>"."</p>";
		echo "</td>";
		echo "</tr>";

	}

	function showOverviewObservation($obsKey, $count, $link, $myList = false) {
		global $dateformat;

		global $objInstrument;
		global $objObject;
		global $objObserver;

		$value = $_SESSION['Qobs'][$obsKey];
		$typefield = "class=\"type" . (2 - ($count % 2)) . "\"";
		// OBJECT
		$object = $value['objectname'];
		// OBSERVER
		$observer = $value['observerid'];
		// INSTRUMENT
		$temp = $value['instrumentid'];
		$instrument = $value['instrumentname'];
		$instrumentsize = round($value['instrumentdiameter'], 0);
		if ($instrument == "Naked eye") {
			$instrument = InstrumentsNakedEye;
		}
		// OUTPUT
		$con = $GLOBALS['objObject']->getDsoProperty($object,'con');
		echo ("<tr $typefield>\n
				    <td><a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_object&amp;object=" . urlencode($object) . "\">" . $object . "</a></td>\n
				    <td> " . $GLOBALS[$con] . "</td>\n
				         <td><a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_observer&amp;user=" . urlencode($value['observerid']) . "\">" . $value['observername'] . "</a></td>\n
				         <td><a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_instrument&amp;instrument=" . urlencode($temp) . "\">" . $instrument . " &nbsp;");
		if ($instrument != InstrumentsNakedEye) {
			echo ("(" . $instrumentsize . "&nbsp;mm" . ")");
		}
		echo ("</a></td><td>");
		// DATE
		if (array_key_exists('deepskylog_id', $_SESSION) && $GLOBALS['objObserver']->getUseLocal($_SESSION['deepskylog_id'])) {
			$date = sscanf($this->getDsObservationLocalDate($value['observationid']), "%4d%2d%2d");
		} else {
			$date = sscanf($this->getDsObservationProperty($value['observationid'],'date'), "%4d%2d%2d");
		}
		echo date($dateformat, mktime(0, 0, 0, $date[1], $date[2], $date[0]));
		echo ("</td>\n
				         <td><a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_observation&amp;observation=" . $value['observationid'] . "&amp;QobsKey=" . $obsKey . "&amp;dalm=D\" title=\"" . LangDetail . "\">" . LangDetails);
		if($this->getDsObservationProperty($value['observationid'],'hasDrawing'))
		  echo "&nbsp;+&nbsp;".LangDrawing;
		echo ("</a>&nbsp;");

		echo ("<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_observation&amp;observation=" . $value['observationid'] . "&amp;dalm=AO\" title=\"" . LangAO . "\">");
		echo LangAOText;
		echo ("</a>");
		echo ("&nbsp;");
		if (array_key_exists('deepskylog_id', $_SESSION) && $_SESSION['deepskylog_id']) // LOGGED IN
			{
			if ($this->getLOObservationId($object, $_SESSION['deepskylog_id'], $value['observationid'])) {
				echo ("<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_observation&amp;observation=" . $value['observationid'] . "&amp;dalm=MO\" title=\"" . LangMO . "\">");
				echo LangMOText;
				echo ("</a>&nbsp;");
				echo ("<a href=\"" . $GLOBALS['baseURL'] . "index.php?indexAction=detail_observation&amp;observation=" . $value['observationid'] . "&amp;dalm=LO\" title=\"" . LangLO . "\">");
				echo LangLOText;
				echo ("</a>&nbsp;");
			}
		}
		echo ("</td>");
		if($GLOBALS['myList']) 
		{ $objectid = $this->getDsObservationProperty($value['observationid'],'objectname');
			echo ("<td>");
			$listname = $_SESSION['listname'];
			$observer = $_SESSION['deepskylog_id'];
			$sql = "SELECT Count(observerobjectlist.objectname) As ObjCnt FROM observerobjectlist WHERE observerid = \"$observer\" AND objectname=\"$object\" AND listname=\"$listname\"";
			$run = mysql_query($sql) or die(mysql_error());
			$get = mysql_fetch_object($run);
			if($get->ObjCnt>0)
			{ echo "<a href=\"".$link."&amp;addObservationToList=".urlencode($value['observationid'])."\" title=\"".LangViewObservationField44."\">E</a>";
			  echo "&nbsp;-&nbsp;";
				echo "<a href=\"".$link."&amp;removeObjectFromList=".urlencode($objectid)."\" title=\"".$object.LangListQueryObjectsMessage3.$_SESSION['listname']."\">R</a>";
			}
			else
      { echo "<a href=\"".$link."&amp;addObjectToList=".urlencode($objectid)."&amp;showname=".urlencode($object)."\" title=\"".$object.LangListQueryObjectsMessage2.$_SESSION['listname']."\">L</a>";
			  echo "&nbsp;-&nbsp;";
				echo "<a href=\"".$link."&amp;addObservationToList=".urlencode($value['observationid'])."\" title=\"".LangViewObservationField44."\">E</a>";
			}
			echo ("</td>");
		}
		echo ("</tr>\n");
	}
}
$objObservation = new Observations;
?>
