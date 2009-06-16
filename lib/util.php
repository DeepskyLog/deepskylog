<?php // util
interface iUtils
{ public  function __construct();
  public  function argoObjects($result);                                       // Creates an argo navis file from an array of objects
  public  function checkAdminOrUserID($toCheck);                               // returns true if logged user equals toCheck, or admin is logged in
  public  function checkArrayKey($theArray,$key,$default='');                  // returns the value of $theArray[$key] or $default if the key doesn't exist
  public  function checkGetDate($year,$month,$day);                            // if year exists (eg 2009), returns 2009xxyy, else if month exists (eg5), returns 05yy 
  public  function checkGetKey($key,$default='');                              // returns the value of $_GET[$key] or $default if the key doesn't exist
  public  function checkGetKeyReturnString($key,$string,$default='');
  public  function checkGetTimeOrDegrees($hr,$min,$sec);
  public  function checkLimitsInclusive($value,$low,$high);
  public  function checkPostKey($key,$default='');                             // returns the value of $_POST[$key] or $default if the key doesn't exist
  public  function checkSessionKey($key,$default='');                          // returns the value of $_SESSION[$key] or $default if the key doesn't exist
  public  function checkUserID($toCheck);                                      // returns true if logged user equals toCheck
  public  function comastObservations($result);                                // Creates a xml file from an array of observations
  public  function csvObjects($result);                                        // Creates a csv file from an array of objects
  public  function csvObservations($result);                                   // Creates a csv file from an array of observations
  public  function csvObservationsImportErrors($result);                       // Creates a csv file from an array of error csv import observations
  public  function pdfCometObservations($result);                              // Creates a pdf document from an array of comet observations
  public  function pdfObjectnames($result);                                    // Creates a pdf document from an array of objects
  public  function pdfObjects($result);                                        // Creates a pdf document from an array of objects
  public  function pdfObjectsDetails($result, $sort='');                       // Creates a pdf detail document from an array of objects
  public  function pdfObservations($result);                                   // Creates a pdf document from an array of observations
  public  function printNewListHeader3(&$list, $link, $min, $step, $total=0,$showNumberOfRecords=true,$showArrows=true);
  public  function printStepsPerPage3($link,$detaillink,$steps=25);
  public  function rssObservations();                                          // Creates an rss feed
  //private function utilitiesCheckIndexActionDSquickPick();                     // returns the includefile if one of the quickpick buttons is pressed
//private function utilitiesCheckIndexActionAdmin($action, $includefile);      // returns the includefile for the specified indexs action after checking it is an admin who is looged in
//private function utilitiesCheckIndexActionAll($action, $includefile);        // returns the includefile for the specified indexs action
//private function utilitiesGetIndexActionDefaultAction();                     // returns the includefile for the specified indexs action
//private function utilitiesCheckIndexActionMember($action, $includefile);     // returns the includefile for the specified indexs action after checking if it is a logged user
  public  function utilitiesDispatchIndexAction();
  public  function utilitiesSetModuleCookie($module);
}
include_once "class.ezpdf.php";
class Utils implements iUtils
{ public  function __construct()
	{ foreach($_POST as $foo => $bar)
      $_POST[$foo]=htmlentities(stripslashes($bar),ENT_COMPAT,"ISO-8859-15",0);
    foreach($_GET as $foo => $bar)
      $_GET[$foo] =htmlentities(stripslashes($bar),ENT_COMPAT,"ISO-8859-15",0);
  }
  public  function argoObjects($result)  // Creates an argo navis file from an array of objects
  { global $objObserver,$loggedUser,$objPresentations,$objAtlas;
    while(list ($key, $valueA) = each($result))
    { echo "DSL ".$valueA['objectname']."|".
           $objPresentations->raArgoToString($valueA['objectra'])."|".
           $objPresentations->decToArgoString($valueA['objectdecl'], 0)."|".
           $GLOBALS["argo".$valueA['objecttype']]."|".
           $objPresentations->presentationInt($valueA['objectmagnitude'],99.9,'') ."|".
           $valueA['objectsize'].";".$objAtlas->atlasCodes[($atlas=$objObserver->getObserverProperty($loggedUser,'standardAtlasCode','urano'))]." ".$valueA[$atlas].";"."CR ".$valueA['objectcontrast'].";".$valueA['objectseen'].";".$valueA['objectlastseen'].
           "\n";
    }
  }
  public  function checkAdminOrUserID($toCheck)
  { global $loggedUser;
    return ((array_key_exists('admin', $_SESSION)&&($_SESSION['admin']=="yes"))||($loggedUser==$toCheck));
  }
  public  function checkArrayKey($theArray,$key,$default='')
  { return (array_key_exists($key,$theArray)&&($theArray[$key]!=''))?$theArray[$key]:$default;
  }
  public  function checkGetDate($year,$month,$day)
  { if($year=$this->checkGetKey($year))
      return sprintf("%04d",$year).sprintf("%02d",$this->checkGetKey($month,'00')).sprintf("%02d",$this->checkGetKey($day,'00'));
    elseif($month=$this->checkGetKey($month))
      return sprintf("%02d",$month).sprintf("%02d",$this->checkGetKey($day,'00'));
  	return '';
  }
  public  function checkGetKey($key,$default='')
  { return (array_key_exists($key,$_GET)&&($_GET[$key]!=''))?$_GET[$key]:$default;
  }
	public  function checkGetKeyReturnString($key,$string,$default='')
  { return array_key_exists($key,$_GET)?$string:$default;
  }
  public  function checkGetTimeOrDegrees($hr,$min,$sec)
  { if($this->checkGetKey($hr).$this->checkGetKey($min).$this->checkGetKey($sec))
      if(substr($this->checkGetKey($hr),0,1)=="-")
	      return -(abs($this->checkGetKey($hr,0))+($this->checkGetKey($min,0)/60)+($this->checkGetKey($sec,0)/3600));
			else
	      return $this->checkGetKey($hr,0)+($this->checkGetKey($min,0)/60)+($this->checkGetKey($sec,0)/3600);
  }
  public  function checkLimitsInclusive($value,$low,$high)
	{ return(($value>=$low)&&($value<=$high));
	}
  public  function checkPostKey($key,$default='')
  { return (array_key_exists($key,$_POST)&&($_POST[$key]!=''))?$_POST[$key]:$default;
  }
  public  function checkRequestKey($key,$default='')
  { return (array_key_exists($key,$_REQUEST)&&($_REQUEST[$key]!=''))?$_REQUEST[$key]:$default;
  }
  public  function checkSessionKey($key,$default='')
  { return (array_key_exists($key,$_SESSION)&&($_SESSION[$key]!=''))?$_SESSION[$key]:$default;
  }
	public  function checkUserID($toCheck)
  { return ($loggedUser==$toCheck);
  }
  public  function comastObservations($result)  // Creates a csv file from an array of observations
  { global $objPresentations, $objObservation;
    include_once "cometobjects.php";
    include_once "observers.php";
    include_once "instruments.php";
    include_once "locations.php";
    include_once "lenses.php";
    include_once "filters.php";
    include_once "cometobservations.php";
    include_once "icqmethod.php";
    include_once "icqreferencekey.php";
    include_once "setup/vars.php";
    include_once "setup/databaseInfo.php";

  $observer = $GLOBALS['objObserver'];
	$location = $GLOBALS['objLocation'];
	
  $dom = new DomDocument('1.0', 'ISO-8859-1');

	$observers = array();
	$sites = array();
	$objects = array();
	$scopes = array();
	$eyepieces = array();
	$lenses = array();
	$filters = array();

    $cntObservers = 0;
    $cntSites = 0;
    $cntObjects = 0;
	$cntScopes = 0;
	$cntEyepieces = 0;
	$cntLens = 0;
	$cntFilter = 0;
	
	$allObs = $result;
	
    while(list ($key, $value) = each($result))
    {
      $obs = $objObservation->getAllInfoDsObservation($value['observationid']);
      $objectname = $obs['objectname'];
      $observerid = $obs['observerid'];
      $inst = $obs['instrumentid'];
      $loc = $obs['locationid'];
      $visibility = $obs['visibility'];
      $seeing = $obs['seeing'];
      $limmag = $obs['limmag'];
      $filt = $obs['filterid'];
      $eyep = $obs['eyepieceid'];
      $lns = $obs['lensid'];

      if (in_array($observerid, $observers) == false) {
      	$observers[$cntObservers] = $observerid;
      	$cntObservers = $cntObservers + 1;
      }

      if (in_array($loc, $sites) == false) {
      	$sites[$cntSites] = $loc;
      	$cntSites = $cntSites + 1;
      }
      
      if (in_array($objectname, $objects) == false) {
      	$objects[$cntObjects] = $objectname;
      	$cntObjects = $cntObjects + 1;
      }

      if (in_array($inst, $scopes) == false) {
      	$scopes[$cntScopes] = $inst;
      	$cntScopes = $cntScopes + 1;
      }

      if (in_array($eyep, $eyepieces) == false) {
      	$eyepieces[$cntEyepieces] = $eyep;
      	$cntEyepieces = $cntEyepieces + 1;
      }

      if (in_array($lns, $lenses) == false) {
      	$lenses[$cntLens] = $lns;
      	$cntLens = $cntLens + 1;
      }

      if (in_array($filt, $filters) == false) {
      	$filters[$cntFilter] = $filt;
      	$cntFilter = $cntFilter + 1;
      }
    }

	// add root fcga -> The header
	$fcgaInfo = $dom->createElement('oal:observations');
	$fcgaDom = $dom->appendChild($fcgaInfo);

    $attr = $dom->createAttribute("version");
    $fcgaInfo->appendChild($attr);

    $attrText = $dom->createTextNode("2.0");
    $attr->appendChild($attrText);
    
	  $attr = $dom->createAttribute("xmlns:oal");
    $fcgaInfo->appendChild($attr);

    $attrText = $dom->createTextNode("http://observation.sourceforge.net/openastronomylog");
    $attr->appendChild($attrText);

    $attr = $dom->createAttribute("xmlns:xsi");
    $fcgaInfo->appendChild($attr);

    $attrText = $dom->createTextNode("http://www.w3.org/2001/XMLSchema-instance");
    $attr->appendChild($attrText);

    $attr = $dom->createAttribute("xsi:schemaLocation");
    $fcgaInfo->appendChild($attr);

    $attrText = $dom->createTextNode("http://observation.sourceforge.net/openastronomylog oal20.xsd");
    $attr->appendChild($attrText);

    //add root - <observers> 
    $observersDom = $fcgaDom->appendChild($dom->createElement('observers')); 

	while(list($key, $value) = each($observers)) 
	{
      $observer2 = $dom->createElement('observer');
      $observerChild = $observersDom->appendChild($observer2);
      $attr = $dom->createAttribute("id");
      $observer2->appendChild($attr);

	  $correctedValue = utf8_encode(html_entity_decode(preg_replace( "/\s+/", "_", $value )));
	  $attrText = $dom->createTextNode("usr_".$correctedValue);
	  $attr->appendChild($attrText);

      $name = $observerChild->appendChild($dom->createElement('name')); 
      $name->appendChild($dom->createCDATASection(utf8_encode(html_entity_decode($observer->getObserverProperty($value,'firstname'))))); 
      
      $surname = $observerChild->appendChild($dom->createElement('surname')); 
      $surname->appendChild($dom->createCDataSection(($observer->getObserverProperty($value,'name')))); 

      $account = $observerChild->appendChild($dom->createElement('account'));
      $account->appendChild($dom->createCDataSection(utf8_encode(html_entity_decode($value))));

      $attr = $dom->createAttribute("name");
      $account->appendChild($attr);

      $attrText = $dom->createTextNode("www.deepskylog.org");
      $attr->appendChild($attrText);
    }
    
    //add root - <sites> 
    $observersDom = $fcgaDom->appendChild($dom->createElement('sites')); 

	while(list($key, $value) = each($sites)) 
	{
      $site2 = $dom->createElement('site');
      $siteChild = $observersDom->appendChild($site2);
      $attr = $dom->createAttribute("id");
      $site2->appendChild($attr);

	  $attrText = $dom->createTextNode("site_" . $value);
	  $attr->appendChild($attrText);

      $name = $siteChild->appendChild($dom->createElement('name')); 
      $name->appendChild($dom->createCDATASection(utf8_encode(html_entity_decode($location->getLocationPropertyFromId($value,'name'))))); 

      $longitude = $siteChild->appendChild($dom->createElement('longitude')); 
      $longitude->appendChild($dom->createTextNode($location->getLocationPropertyFromId($value,'longitude'))); 

      $attr = $dom->createAttribute("unit");
      $longitude->appendChild($attr);

	  $attrText = $dom->createTextNode("deg");
	  $attr->appendChild($attrText);


      $latitude = $siteChild->appendChild($dom->createElement('latitude')); 
      $latitude->appendChild($dom->createTextNode($location->getLocationPropertyFromId($value,'latitude'))); 

      $attr = $dom->createAttribute("unit");
      $latitude->appendChild($attr);

	  $attrText = $dom->createTextNode("deg");
	  $attr->appendChild($attrText);


      $timezone = $siteChild->appendChild($dom->createElement('timezone'));
      $dateTimeZone = new DateTimeZone($location->getLocationPropertyFromId($value,'timezone'));
	  $datestr = "01/01/2008";
	  $dateTime = new DateTime($datestr, $dateTimeZone);
	  // Geeft tijdsverschil terug in seconden
	  $timedifference = $dateTimeZone->getOffset($dateTime);
	  $timedifference = $timedifference / 60.0; 
      $timezone->appendChild($dom->createTextNode($timedifference)); 
    }

    //add root - <sessions>  DeepskyLog has no sessions
    $observersDom = $fcgaDom->appendChild($dom->createElement('sessions')); 

    //add root - <targets> 
    $observersDom = $fcgaDom->appendChild($dom->createElement('targets')); 

	while(list($key, $value) = each($objects)) 
	{
      $object2 = $dom->createElement('target');
      $objectChild = $observersDom->appendChild($object2);
      $attr = $dom->createAttribute("id");
      $object2->appendChild($attr);

	  $correctedValue = utf8_encode(html_entity_decode(preg_replace( "/\s+/", "_", $value )));
	  $correctedValue = utf8_encode(html_entity_decode(preg_replace( "/\+/", "_", $correctedValue )));
	  $correctedValue = utf8_encode(html_entity_decode(preg_replace( "/\//", "_", $correctedValue )));
	  $correctedValue = utf8_encode(html_entity_decode(preg_replace( "/\,/", "_", $correctedValue )));

	  $attrText = $dom->createTextNode("_" . $correctedValue);
	  $attr->appendChild($attrText);

      $attr = $dom->createAttribute("xsi:type");
      $object2->appendChild($attr);

      $object = $GLOBALS['objObject']->getAllInfoDsObject($value);

	  $type = $object["type"];
	  if ($type == "OPNCL" || $type == "SMCOC" || $type == "LMCOC")
	  {
	  	$type = "oal:deepSkyOC";
	  } else if ($type == "GALXY") {
	  	$type = "oal:deepSkyGX";
    } else if ($type == "GALCL") {
      $type = "oal:deepSkyCG";
	  } else if ($type == "PLNNB") {
	  	$type = "oal:deepSkyPN";
	  } else if ($type == "ASTER" || $type == "AA1STAR" || $type == "AA2STAR" 
	      || $type == "AA3STAR" || $type == "AA4STAR" || $type == "AA8STAR"
	      || $type == "DS") {
	  	$type = "oal:deepSkyAS";
	  } else if ($type == "GLOCL" || $type == "GXAGC" || $type == "LMCGC" 
	      || $type == "SMCGC") {
	  	$type = "oal:deepSkyGC";
	  } else if ($type == "BRTNB" || $type == "CLANB" || $type == "EMINB"
	      || $type == "ENRNN" || $type == "ENSTR" || $type == "GXADN"
	      || $type == "GACAN" || $type == "HII" || $type == "LMCCN"
	      || $type == "LMCDN" || $type == "REFNB" || $type == "RNHII"
	      || $type == "SMCCN" || $type == "SMCDN" || $type == "SNREM"
	      || $type == "STNEB" || $type == "WRNEB") {
	  	$type = "oal:deepSkyGN";
	  } else if ($type == "QUASR") {
	  	$type = "oal:deepSkyQS";
	  } else if ($type == "DRKNB") {
	  	$type = "oal:deepSkyDN";
	  } else if ($type == "NONEX") {
	  	$type = "oal:deepSkyNA";
	  }
	  $attrText = $dom->createTextNode($type);
	  $attr->appendChild($attrText);

      $datasource = $objectChild->appendChild($dom->createElement('datasource')); 
      $datasource->appendChild($dom->createCDATASection(utf8_encode(html_entity_decode($object["datasource"])))); 
      
      $name = $objectChild->appendChild($dom->createElement('name')); 
      $name->appendChild($dom->createCDATASection(($value)));
      
      $altnames = $GLOBALS['objObject']->getAlternativeNames($value);
      while(list($key2, $value2) = each($altnames)) // go through names array
  	  { if(trim($value2)!=trim($value))
  	  	{
  	  	  if (trim($value2) != "") {
            $alias = $objectChild->appendChild($dom->createElement('alias')); 
            $alias->appendChild($dom->createCDataSection((trim($value2))));
  	  	  }
  	  	} 
      }

      $position = $objectChild->appendChild($dom->createElement('position')); 

	  $raDom = $dom->createElement('ra');
      $ra = $position->appendChild($raDom); 
      $ra->appendChild($dom->createTextNode($object["ra"] * 15.0));

      $attr = $dom->createAttribute("unit");
      $raDom->appendChild($attr);

	  $attrText = $dom->createTextNode("deg");
	  $attr->appendChild($attrText);

	  $decDom = $dom->createElement('dec');
      $dec = $position->appendChild($decDom); 
      $dec->appendChild($dom->createTextNode($object["decl"]));

      $attr = $dom->createAttribute("unit");
      $decDom->appendChild($attr);

	  $attrText = $dom->createTextNode("deg");
	  $attr->appendChild($attrText);
	  
	  $constellation = $objectChild->appendChild($dom->createElement('constellation')); 
      $constellation->appendChild($dom->createCDATASection(($object["con"])));

  	  if ($object["diam2"] > 0.0 && $object["diam2"] != 99.9) {
	  	$sdDom = $dom->createElement('smallDiameter');
	  	$diam2 = $objectChild->appendChild($sdDom);
	  	$sDiameter = $object["diam2"] / 60.0;
      	$diam2->appendChild($dom->createTextNode($sDiameter));

        $attr = $dom->createAttribute("unit");
        $sdDom->appendChild($attr);

	    $attrText = $dom->createTextNode("arcmin");
	    $attr->appendChild($attrText);
	  }

      $diameter1 = $object["diam1"];
	  if ($diameter1 > 0.0 && $diameter1 != 99.9) {
	  	$ldDom = $dom->createElement('largeDiameter');
	  	$diam1 = $objectChild->appendChild($ldDom);
	  	$lDiameter = $diameter1 / 60.0;
      	$diam1->appendChild($dom->createTextNode($lDiameter));

        $attr = $dom->createAttribute("unit");
        $ldDom->appendChild($attr);

	    $attrText = $dom->createTextNode("arcmin");
	    $attr->appendChild($attrText);
	  }

	  if ($object["mag"] < 99.0) {
	  	$mag = $objectChild->appendChild($dom->createElement('visMag')); 
      	$mag->appendChild($dom->createTextNode(($object["mag"])));
	  }
	  
	  if ($object["subr"] < 99.0) {
	  	$mag = $objectChild->appendChild($dom->createElement('surfBr')); 
      	$mag->appendChild($dom->createTextNode(($object["subr"])));

      	$attr = $dom->createAttribute("unit");
        $mag->appendChild($attr);

    $attrText = $dom->createTextNode("mags-per-squarearcmin");
    $attr->appendChild($attrText);
	  }

	  if ($type != "oal:deepSkyCG" && $type != "oal:deepSkyGC" && $type != "oal:deepSkyNA" &&
	       $type != "oal:deepSkyOC" && $type != "oal:deepSkyPN" && $type != "oal:deepSkyQS") {
	    if ($object["pa"] < 999.0) {
	  	  $pa = $objectChild->appendChild($dom->createElement('pa')); 
      	  $pa->appendChild($dom->createTextNode(($object["pa"])));
	    }
	  }
	}
    //add root - <scopes> 
    $observersDom = $fcgaDom->appendChild($dom->createElement('scopes')); 

	while(list($key, $value) = each($scopes)) 
	{
      if ($GLOBALS['objInstrument']->getInstrumentPropertyFromId($value,'name') != "") {
        $scope2 = $dom->createElement('scope');
        $siteChild = $observersDom->appendChild($scope2);
        $attr = $dom->createAttribute("id");
        $scope2->appendChild($attr);
      

	    $attrText = $dom->createTextNode("opt_" . $value);
	    $attr->appendChild($attrText);

        $attr = $dom->createAttribute("xsi:type");
        $scope2->appendChild($attr);

	    if ($GLOBALS['objInstrument']->getInstrumentPropertyFromId($value,'fixedMagnification') > 0) {
	  	  $typeLong = "oal:fixedMagnificationOpticsType";
	    } else {
	  	  $typeLong = "oal:scopeType";	  	
	    }
	    $tp = $GLOBALS['objInstrument']->getInstrumentPropertyFromId($value,'type');
	    if ($tp == InstrumentOther || $tp == InstrumentRest) {
	  	  $typeShort = "";
	    } else if ($tp == InstrumentNakedEye) {
	  	  $typeShort = "A";
	    } else if ($tp == InstrumentBinoculars || $tp == InstrumentFinderscope) {
	  	  $typeShort = "B";
	    } else if ($tp == InstrumentRefractor) {
	  	  $typeShort = "R";
	    } else if ($tp == InstrumentReflector) {
	  	  $typeShort = "N";
	    } else if ($tp == InstrumentCassegrain) {
	  	  $typeShort = "C";
	    } else if ($tp == InstrumentKutter) {
	  	  $typeShort = "K";
	    } else if ($tp == InstrumentMaksutov) {
	  	  $typeShort = "M";
	    } else if ($tp == InstrumentSchmidtCassegrain) {
	  	  $typeShort = "S";
	    }

	    $attrText = $dom->createTextNode($typeLong);
	    $attr->appendChild($attrText);

        $name = $siteChild->appendChild($dom->createElement('model')); 
        $name->appendChild($dom->createCDATASection(utf8_encode(html_entity_decode($GLOBALS['objInstrument']->getInstrumentPropertyFromId($value,'name'))))); 

        $type = $siteChild->appendChild($dom->createElement('type')); 
        $type->appendChild($dom->createCDATASection(($typeShort))); 

        $aperture = $siteChild->appendChild($dom->createElement('aperture')); 
        $aperture->appendChild($dom->createTextNode(($GLOBALS['objInstrument']->getInstrumentPropertyFromId($value,'diameter')))); 

	    if ($GLOBALS['objInstrument']->getInstrumentPropertyFromId($value,'fixedMagnification') > 0) {
      	  $magnification = $siteChild->appendChild($dom->createElement('magnification'));
          $magnification->appendChild($dom->createTextNode(($GLOBALS['objInstrument']->getInstrumentPropertyFromId($value,'fixedMagnification')))); 
	    } else {
      	  $focalLength = $siteChild->appendChild($dom->createElement('focalLength'));
          $focalLength->appendChild($dom->createTextNode(($GLOBALS['objInstrument']->getInstrumentPropertyFromId($value,'fd')) * $GLOBALS['objInstrument']->getInstrumentPropertyFromId($value,'diameter'))); 
	    }
      }
	  }

    //add root - <eyepieces> 
    $observersDom = $fcgaDom->appendChild($dom->createElement('eyepieces')); 

	while(list($key, $value) = each($eyepieces)) 
	{
	  if ($value != "" && $value > 0) {
        $eyepiece2 = $dom->createElement('eyepiece');
        $eyepieceChild = $observersDom->appendChild($eyepiece2);
        $attr = $dom->createAttribute("id");
        $eyepiece2->appendChild($attr);

	    $attrText = $dom->createTextNode("ep_" . $value);
	    $attr->appendChild($attrText);

        $model = $eyepieceChild->appendChild($dom->createElement('model')); 
        $model->appendChild($dom->createCDATASection(utf8_encode(html_entity_decode($GLOBALS['objEyepiece']->getEyepiecePropertyFromId($value,'name'))))); 

        $focalLength = $eyepieceChild->appendChild($dom->createElement('focalLength')); 
        $focalLength->appendChild($dom->createTextNode(($GLOBALS['objEyepiece']->getEyepiecePropertyFromId($value,'focalLength'))));

		if ($GLOBALS['objEyepiece']->getEyepiecePropertyFromId($value,'maxFocalLength') > 0) {
          $maxFocalLength = $eyepieceChild->appendChild($dom->createElement('maxFocalLength')); 
          $maxFocalLength->appendChild($dom->createTextNode(($GLOBALS['objEyepiece']->getEyepiecePropertyFromId($value,'maxFocalLength'))));
		}

        $apparentFOV = $eyepieceChild->appendChild($dom->createElement('apparentFOV')); 
        $apparentFOV->appendChild($dom->createTextNode(($GLOBALS['objEyepiece']->getEyepiecePropertyFromId($value,'apparentFOV'))));

        $attr = $dom->createAttribute("unit");
        $apparentFOV->appendChild($attr);

	    $attrText = $dom->createTextNode("deg");
	    $attr->appendChild($attrText);
      }
    }

    //add root - <lenses> 
    $observersDom = $fcgaDom->appendChild($dom->createElement('lenses')); 

	while(list($key, $value) = each($lenses)) 
	{
	  if ($value != "" && $value > 0) {
        $lens2 = $dom->createElement('lens');
        $lensChild = $observersDom->appendChild($lens2);
        $attr = $dom->createAttribute("id");
        $lens2->appendChild($attr);

	    $attrText = $dom->createTextNode("le_" . $value);
	    $attr->appendChild($attrText);

        $model = $lensChild->appendChild($dom->createElement('model')); 
        $model->appendChild($dom->createCDATASection(utf8_encode(html_entity_decode($GLOBALS['objLens']->getLensPropertyFromId($value,'name'))))); 

        $factor = $lensChild->appendChild($dom->createElement('factor')); 
        $factor->appendChild($dom->createTextNode(($GLOBALS['objLens']->getLensPropertyFromId($value,'factor'))));
      }
    }

    //add root - <filters> 
    $observersDom = $fcgaDom->appendChild($dom->createElement('filters')); 

	while(list($key, $value) = each($filters)) 
	{
	  if ($value != "" && $value > 0) {
        $filter2 = $dom->createElement('filter');
        $filterChild = $observersDom->appendChild($filter2);
        $attr = $dom->createAttribute("id");
        $filter2->appendChild($attr);

	    $attrText = $dom->createTextNode("flt_" . $value);
	    $attr->appendChild($attrText);
 
        $model = $filterChild->appendChild($dom->createElement('model')); 
        $model->appendChild($dom->createCDATASection(utf8_encode(html_entity_decode($GLOBALS['objFilter']->getFilterPropertyFromId($value,'name'))))); 

		$tp = $GLOBALS['objFilter']->getFilterPropertyFromId($value,'type');
		if ($tp == 0) {
			$filType = "other";
		} else if ($tp == 1) {
			$filType = "broad band";
		} else if ($tp == 2) {
			$filType = "narrow band";
		} else if ($tp == 3) {
			$filType = "O-III";
		} else if ($tp == 4) {
			$filType = "H-beta";
		} else if ($tp == 5) {
			$filType = "H-alpha";
		} else if ($tp == 6) {
			$filType = "color";
		} else if ($tp == 7) {
			$filType = "neutral";
		} else if ($tp == 8) {
			$filType = "corrective";
		}

        $type = $filterChild->appendChild($dom->createElement('type')); 
        $type->appendChild($dom->createCDATASection($filType));

		if ($filType == "color") {
			$col = $GLOBALS['objFilter']->getFilterPropertyFromId($value,'color');
			if ($col == 1) {
				$colName = "light red";
			} else if ($col == 2) {
				$colName = "red";
			} else if ($col == 3) {
				$colName = "deep red";
			} else if ($col == 4) {
				$colName = "orange";
			} else if ($col == 5) {
				$colName = "light yellow";
			} else if ($col == 6) {
				$colName = "deep yellow";
			} else if ($col == 7) {
				$colName = "yellow";
			} else if ($col == 8) {
				$colName = "yellow-green";
			} else if ($col == 9) {
				$colName = "light green";
			} else if ($col == 10) {
				$colName = "green";
			} else if ($col == 11) {
				$colName = "medium blue";
			} else if ($col == 12) {
				$colName = "pale blue";
			} else if ($col == 13) {
				$colName = "blue";
			} else if ($col == 14) {
				$colName = "deep blue";
			} else if ($col == 15) {
				$colName = "violet";
			} 
			if ($colName != "") {
              $color = $filterChild->appendChild($dom->createElement('color')); 
              $color->appendChild($dom->createCDATASection($colName));
			}
			
			if ($GLOBALS['objFilter']->getFilterPropertyFromId($value,'wratten') != "") {
		      $wratten = $filterChild->appendChild($dom->createElement('wratten')); 
              $wratten->appendChild($dom->createCDATASection($GLOBALS['objFilter']->getFilterPropertyFromId($value,'wratten')));
			}

			if ($GLOBALS['objFilter']->getFilterPropertyFromId($value,'schott') != "") {
		      $schott = $filterChild->appendChild($dom->createElement('schott')); 
              $schott->appendChild($dom->createCDATASection($GLOBALS['objFilter']->getFilterPropertyFromId($value,'schott')));
			}
		}
      }
    }

    //add root - <imagers>  DeepskyLog has no imagers
    $observersDom = $fcgaDom->appendChild($dom->createElement('imagers')); 

	// Add the observations.
	while(list ($key, $value) = each($allObs))
    {
      $obs = $GLOBALS['objObservation']->getAllInfoDsObservation($value['observationid']);
      $objectname = $obs['objectname'];
      $observerid = $obs['observerid'];
      $inst = $obs['instrumentid'];
      $loc = $obs['locationid'];
      $visibility = $obs['visibility'];
      $seeing = $obs['seeing'];
      $limmag = $obs['limmag'];
      $filt = $obs['filterid'];
      $eyep = $obs['eyepieceid'];
      $lns = $obs['lensid'];

      $observation = $fcgaDom->appendChild($dom->createElement('observation')); 
	  $attr = $dom->createAttribute("id");
      $observation->appendChild($attr);

	  $attrText = $dom->createTextNode("obs_" . $value['observationid']);
	  $attr->appendChild($attrText);

	  $correctedValue = utf8_encode(html_entity_decode(preg_replace( "/\s+/", "_", $observerid )));
      $observer = $observation->appendChild($dom->createElement('observer')); 
      $observer->appendChild($dom->createTextNode("usr_" . $correctedValue));
	  
      $site = $observation->appendChild($dom->createElement('site')); 
      $site->appendChild($dom->createTextNode("site_" . $loc));

      $target = $observation->appendChild($dom->createElement('target')); 
      $correctedValue = utf8_encode(html_entity_decode(preg_replace( "/\s+/", "_", $objectname )));
	  $correctedValue = utf8_encode(html_entity_decode(preg_replace( "/\+/", "_", $correctedValue )));
	  $correctedValue = utf8_encode(html_entity_decode(preg_replace( "/\//", "_", $correctedValue )));
	  $correctedValue = utf8_encode(html_entity_decode(preg_replace( "/\,/", "_", $correctedValue )));
      
      $target->appendChild($dom->createTextNode("_" . $correctedValue));

	  if ($obs["time"] > 0)
	  {
	  	$time = sprintf("T%02d:%02d:00+00:00", (int)($obs["time"] / 100), $obs["time"] - (int)($obs["time"] / 100) * 100);
	  } else {
	  	$time = "T22:00:00+00:00";
	  }

	  $year = (int)($obs["date"] / 10000);
	  $month = (int)(($obs["date"] - $year * 10000) / 100);
	  $day = (int)(($obs["date"] - $year * 10000 - $month * 100));
	  $date = sprintf("%4d-%02d-%02d", $year, $month, $day);

      $begin = $observation->appendChild($dom->createElement('begin')); 
      $begin->appendChild($dom->createTextNode($date . $time));

	  if ($obs["limmag"] > 0) {
        $faintestStar = $observation->appendChild($dom->createElement('faintestStar')); 
        $faintestStar->appendChild($dom->createTextNode($obs["limmag"]));
	  } else if ($obs["SQM"] > 0) {
        $magPerSquareArcsecond = $observation->appendChild($dom->createElement('sky-quality')); 
        $magPerSquareArcsecond->appendChild($dom->createTextNode($obs["SQM"]));

        $attr = $dom->createAttribute("unit");
        $magPerSquareArcsecond->appendChild($attr);

    $attrText = $dom->createTextNode("mags-per-squarearcsec");
    $attr->appendChild($attrText);
	  }

	  if ($obs["seeing"] > 0) {
        $seeing = $observation->appendChild($dom->createElement('seeing')); 
        $seeing->appendChild($dom->createTextNode($obs["seeing"]));
	  }

      $scope = $observation->appendChild($dom->createElement('scope')); 
      $scope->appendChild($dom->createTextNode("opt_" . $inst));
 	  
 	  if ($eyep > 0) {
        $eyepiece = $observation->appendChild($dom->createElement('eyepiece')); 
        $eyepiece->appendChild($dom->createTextNode("ep_" . $eyep));
 	  }

	  if ($lns > 0) {
        $lens = $observation->appendChild($dom->createElement('lens')); 
        $lens->appendChild($dom->createTextNode("le_" . $lns));
	  }

	  if ($filt > 0) {
        $filter = $observation->appendChild($dom->createElement('filter')); 
        $filter->appendChild($dom->createTextNode("flt_" . $filt));
	  }

	  $magni = 0;
	  if ($GLOBALS['objInstrument']->getInstrumentPropertyFromId($inst,'fixedMagnification') > 0)
	  {
	  	$magni = $GLOBALS['objInstrument']->getInstrumentPropertyFromId($inst,'fixedMagnification');
	  } else if ($obs["magnification"] > 0) {
	    $magni = $obs["magnification"];
    } else if ($eyep > 0 && $GLOBALS['objInstrument']->getInstrumentPropertyFromId($inst,'fixedMagnification') > 0) {
	  	$factor = 1.0;
	  	if ($GLOBALS['objLens']->getFilterPropertyFromId($lns,'factor') > 0) {
	  		$factor = $GLOBALS['objLens']->getFilterPropertyFromId($lns,'factor');
	  	}
		$magni = sprintf("%.2f", $GLOBALS['objInstrument']->getInstrumentPropertyFromId($inst,'fixedMagnification') * $GLOBALS['objInstrument']->getInstrumentPropertyFromId($inst,'diameter') 
		        * $factor / $GLOBALS['objEyepiece']->getEyepiecePropertyFromId($eyep,'focalLength'));
	  }
	  
	  if ($magni > 0) {
        $magnification = $observation->appendChild($dom->createElement('magnification')); 
        $magnification->appendChild($dom->createTextNode($magni));
	  }

      $result = $observation->appendChild($dom->createElement('result'));

	  if ($obs["extended"] > 0)
	  {
	    $attr = $dom->createAttribute("extended");
        $result->appendChild($attr);

	    $attrText = $dom->createTextNode("true");
	    $attr->appendChild($attrText);
	  }

	  $attr = $dom->createAttribute("lang");
      $result->appendChild($attr);

	  $attrText = $dom->createTextNode($obs["language"]);
      $attr->appendChild($attrText);

	  if ($obs["mottled"] > 0)
	  {
	    $attr = $dom->createAttribute("mottled");
        $result->appendChild($attr);

	    $attrText = $dom->createTextNode("true");
	    $attr->appendChild($attrText);
	  }

    if ($type == "OPNCL" || $type == "SMCOC" || $type == "LMCOC")
    {
	    if ($obs["partlyUnresolved"] > 0)
	    {
	      $attr = $dom->createAttribute("partlyUnresolved");
        $result->appendChild($attr);

	      $attrText = $dom->createTextNode("true");
	      $attr->appendChild($attrText);
	    }

      if ($obs["unusualShape"] > 0)
      {
        $attr = $dom->createAttribute("unusualShape");
        $result->appendChild($attr);

        $attrText = $dom->createTextNode("true");
        $attr->appendChild($attrText);
      }

      if ($obs["colorContrasts"] > 0)
      {
        $attr = $dom->createAttribute("colorContrasts");
          $result->appendChild($attr);

        $attrText = $dom->createTextNode("true");
        $attr->appendChild($attrText);
      }   
    }

	  if ($obs["resolved"] > 0)
	  {
	    $attr = $dom->createAttribute("resolved");
        $result->appendChild($attr);

	    $attrText = $dom->createTextNode("true");
	    $attr->appendChild($attrText);
	  }

	  if ($obs["stellar"] > 0)
	  {
	    $attr = $dom->createAttribute("stellar");
        $result->appendChild($attr);

	    $attrText = $dom->createTextNode("true");
	    $attr->appendChild($attrText);
	  }

	  $attr = $dom->createAttribute("xsi:type");
      $result->appendChild($attr);


      $object = $GLOBALS['objObject']->getAllInfoDsObject($objectname);

	  $type = $object["type"];
	  if ($type == "OPNCL" || $type == "SMCOC" || $type == "LMCOC")
	  {
	  	$type = "oal:findingsDeepSkyOCType";
	  } else {
	  	$type = "oal:findingsDeepSkyType";	  	
	  }
	  $attrText = $dom->createTextNode($type);
	  $attr->appendChild($attrText);

      $description = $result->appendChild($dom->createElement('description')); 
      $description->appendChild($dom->createCDATASection(utf8_encode($objPresentations->br2nl(html_entity_decode($obs["description"])))));

      $rat = $obs["visibility"];
      if ($rat == 0) {
      	$rat = 99;
      }

	  if ($obs["smallDiameter"] > 0) {
        $smallDiameter = $result->appendChild($dom->createElement('smallDiameter')); 
        $smallDiameter->appendChild($dom->createTextNode($obs["smallDiameter"]));

        $attr = $dom->createAttribute("unit");
        $smallDiameter->appendChild($attr);

        $attrText = $dom->createTextNode("arcsec");
        $attr->appendChild($attrText);
	  }

  	  if ($obs["largeDiameter"] > 0) {
        $largeDiameter = $result->appendChild($dom->createElement('largeDiameter')); 
        $largeDiameter->appendChild($dom->createTextNode($obs["largeDiameter"]));

        $attr = $dom->createAttribute("unit");
        $largeDiameter->appendChild($attr);

        $attrText = $dom->createTextNode("arcsec");
        $attr->appendChild($attrText);
	  }

      $rating = $result->appendChild($dom->createElement('rating')); 
      $rating->appendChild($dom->createTextNode($rat));

	  if ($obs["clusterType"] != "" && $obs["clusterType"] != 0) {
        $character = $result->appendChild($dom->createElement('character')); 
        $character->appendChild($dom->createCDATASection($obs["clusterType"]));
  	  }
    }

    //generate xml 
    $dom->formatOutput = true; // set the formatOutput attribute of 
                               // domDocument to true 
    // save XML as string or file 
    $test1 = $dom->saveXML(); // put string in test1 
 
  	print $test1;
  }
  public  function csvObjects($result)  // Creates a csv file from an array of objects
  { global $objObject,$objPresentations,$objObserver, $loggedUser;
    echo html_entity_decode(LangCSVMessage7)."\n";
    while(list($key,$valueA)=each($result))
    { $alt="";
      $alts=$objObject->getAlternativeNames($valueA['objectname']);
      while(list($key,$value)=each($alts))
        if($value!=$valueA['objectname'])
          $alt.=" - ".trim($value);
      $alt=($alt?substr($alt,3):'');
      echo $valueA['objectname'].";". 
           $alt.";".
           $objPresentations->raToString($valueA['objectra']).";".
           $objPresentations->decToString($valueA['objectdecl'], 0).";".
           $GLOBALS[$valueA['objectconstellation']].";".
           $GLOBALS[$valueA['objecttype']].";".
           $objPresentations->presentationInt1($valueA['objectmagnitude'],99.9,'').";".
           $objPresentations->presentationInt1($valueA['objectsurfacebrightness'],99.9,'').";".
           $valueA['objectsize'].";".
           $objPresentations->presentationInt($valueA['objectpa'],999,'').";".
           $valueA[$objObserver->getObserverProperty($loggedUser,'standardAtlasCode','urano')].";".
           $valueA['objectcontrast'].";".
           $valueA['objectoptimalmagnification'].";".
           $valueA['objectseen'].";".
           $valueA['objectlastseen'].
           "\n";
    }
  }  
  public  function csvObservations($result)  // Creates a csv file from an array of observations
  { global $objLens, $objFilter, $objEyepiece, $objLocation,$objPresentations,$objObservation,$objObserver, $objInstrument;
    while(list($key,$value)=each($result))
    { $obs=$objObservation->getAllInfoDsObservation($value['observationid']);
      $date=sscanf($obs['date'], "%4d%2d%2d");
      $time=$obs['time'];
      if($time>="0")
      { $hours=(int)($time/100);
        $minutes=$time-(100*$hours);
        $time=sprintf("%d:%02d",$hours,$minutes);
      }
      else
        $time = "";
      echo html_entity_decode($obs['objectname']).";". 
           html_entity_decode($objObserver->getObserverProperty($obs['observerid'],'firstname'). " ".$objObserver->getObserverProperty($obs['observerid'],'name')).";". 
           $date[2]."-".$date[1]."-".$date[0].";".
           $time.";". 
           html_entity_decode($objLocation->getLocationPropertyFromId($obs['locationid'],'name')).";". 
           html_entity_decode($objInstrument->getInstrumentPropertyFromId($obs['instrumentid'],'name')).";". 
           html_entity_decode($objEyepiece->getEyepiecePropertyFromId($obs['eyepieceid'],'name')).";". 
           html_entity_decode($objFilter->getFilterPropertyFromId($obs['filterid'],'name')).";".
           html_entity_decode($objLens->getLensPropertyFromId($obs['lensid'],'name')).";". 
           $obs['seeing'].";". 
           $obs['limmag'].";". 
           $objPresentations->presentationInt($obs['visibility'],"0","").";". 
           $obs['language'].";". 
           preg_replace("/(\")/", "", preg_replace("/(\r\n|\n|\r)/", "", preg_replace("/;/", ",",$objPresentations->br2nl(html_entity_decode($obs['description']))))). 
           "\n";
    }
  }  
  public  function csvObservationsImportErrors($result)  // Creates a csv file from an array of error csv import observations
  { global $objLens, $objFilter, $objEyepiece, $objLocation,$objPresentations,$objObservation,$objObserver, $objInstrument;
    for($i=0;$i<count($_SESSION['csvImportErrorData']);$i++)
    { for($j=0;$j<13;$j++) 
        echo $_SESSION['csvImportErrorData'][$i][$j].";";
      echo preg_replace("/(\")/", "", preg_replace("/(\r\n|\n|\r)/", "", preg_replace("/;/", ",",$objPresentations->br2nl(html_entity_decode($_SESSION['csvImportErrorData'][$i][13])))));
      echo "\n";
    }
  }
  public  function pdfCometObservations($result)// Creates a pdf document from an array of comet observations
  { include_once "cometobjects.php";
    include_once "observers.php";
    include_once "instruments.php";
    include_once "locations.php";
    include_once "cometobservations.php";
    include_once "icqmethod.php";
    include_once "icqreferencekey.php";
    include_once "setup/vars.php";
    include_once "setup/databaseInfo.php";
    global $instDir,$objCometObject,$loggedUser;
    $objects = new CometObjects;
    $observer = new Observers;
    $instrument = new Instruments;
    $observation = new CometObservations;
    $location = new Locations;
    $util = $this;
    $ICQMETHODS = new ICQMETHOD();
    $ICQREFERENCEKEYS = new ICQREFERENCEKEY();
    $_GET['pdfTitle']="CometObservations.pdf";
    // Create pdf file
    $pdf = new Cezpdf('a4', 'portrait');
    $pdf->ezStartPageNumbers(300, 30, 10);

    $fontdir = $instDir.'lib/fonts/Helvetica.afm';
    $pdf->selectFont($fontdir);
    $pdf->ezText(html_entity_decode(LangPDFTitle3)."\n");

    while(list ($key, $value) = each($result))
    {
      $objectname = $objCometObject->getName($observation->getObjectId($value));

      $pdf->ezText($objectname, "14");

      $observerid = $observation->getObserverId($value);

      if ($observer->getObserverProperty($loggedUser,'UT'))
      { $date = sscanf($observation->getDate($value), "%4d%2d%2d");
        $time = $observation->getTime($value);
      }
      else
      { $date = sscanf($observation->getLocalDate($value), "%4d%2d%2d");
        $time = $observation->getLocalTime($value);
      }
      $hour = (int)($time / 100);
      $minute = $time - $hour * 100;
      $formattedDate = date($GLOBALS['dateformat'], mktime(0,0,0,$date[1],$date[2],$date[0]));

      if ($minute < 10)
      {
        $minute = "0".$minute;
      }

      $observername = LangPDFMessage13.$observer->getObserverProperty($observerid,'firstname')." ".$observer->getObserverProperty($observerid,'name').html_entity_decode(LangPDFMessage14).$formattedDate." (".$hour.":".$minute.")";

       
      $pdf->ezText($observername, "12");


      // Location and instrument
      if (($observation->getLocationId($value) != 0 && $observation->getLocationId($value) != 1) || $observation->getInstrumentId($value) != 0)
      {
        if ($observation->getLocationId($value) != 0 && $observation->getLocationId($value) != 1)
        {
          $locationname = LangPDFMessage10." : ".$location->getLocationPropertyFromId($observation->getLocationId($value),'name');
          $extra = ", ";
        }
        else
        {
          $locationname = "";
        }

        if ($observation->getInstrumentId($value) != 0)
        {
          $instr = $instrument->getInstrumentPropertyFromId($observation->getInstrumentId($value),'name');
          if ($instr == "Naked eye")
          {
            $instr = InstrumentsNakedEye;
          }

          $locationname = $locationname.$extra.html_entity_decode(LangPDFMessage11)." : ".$instr;

          if (strcmp($observation->getMagnification($value), "") != 0)
          {
            $locationname = $locationname." (".$observation->getMagnification($value)." x)";
          }
        }

        $pdf->ezText($locationname, "12");
      }

      // Methode
      $method = $observation->getMethode($value);

      if (strcmp($method, "") != 0)
      {
        $methodstr = html_entity_decode(LangViewObservationField15)." : ".$method." - ".$ICQMETHODS->getDescription($method);

        $pdf->ezText($methodstr, "12");
      }

      // Used chart
      $chart = $observation->getChart($value);

      if (strcmp($chart, "") != 0)
      {
        $chartstr = html_entity_decode(LangViewObservationField17)." : ".$chart." - ".$ICQREFERENCEKEYS->getDescription($chart);

        $pdf->ezText($chartstr, "12");
      }

      // Magnitude
      $magnitude = $observation->getMagnitude($value);

      if ($magnitude != -99.9)
      {
        $magstr = "";

        if ($observation->getMagnitudeWeakerThan($value))
        {
          $magstr = $magstr.LangNewComet3." ";
        }
        $magstr = $magstr.html_entity_decode(LangViewObservationField16)." : ".sprintf("%.01f", $magnitude);

        if ($observation->getMagnitudeUncertain($value))
        {
          $magstr = $magstr." (".LangNewComet2.")";
        }

        $pdf->ezText($magstr, "12");
      }
       
      // Degree of condensation
      $dc = $observation->getDc($value);
      $coma = $observation->getComa($value);

      $dcstr = "";
      $extra = "";

      if (strcmp($dc, "") != 0 || $coma != -99)
      {
        if (strcmp($dc, "") != 0)
        {
          $dcstr = $dcstr.html_entity_decode(LangNewComet8)." : ".$dc;
          $extra = ", ";
        }

        // Coma

        if ($coma != -99)
        {
          $dcstr = $dcstr.$extra.html_entity_decode(LangNewComet9)." : ".$coma."'";
        }

        $pdf->ezText($dcstr, "12");
      }

      // Tail
      $tail = $observation->getTail($value);
      $pa = $observation->getPa($value);

      $tailstr = "";
      $extra = "";

      if ($tail != -99 || $pa != -99)
      {
        if ($tail != -99)
        {
          $tailstr = $tailstr.html_entity_decode(LangNewComet10)." : ".$tail."'";
          $extra = ", ";
        }

        if ($pa != -99)
        {
          $tailstr = $tailstr.$extra.html_entity_decode(LangNewComet11)." : ".$pa."";
        }

        $pdf->ezText($tailstr, "12");
      }

      // Description
      $description = $observation->getDescription($value);

      if (strcmp($description, "") != 0)
      {
        $descstr = html_entity_decode(LangPDFMessage15)." : ".strip_tags($description);
        $pdf->ezText($descstr, "12");
      }


      $upload_dir = $GLOBALS['instDir'].'comets/'.'cometdrawings';
      $dir = opendir($upload_dir);

      while (FALSE !== ($file = readdir($dir)))
      {
        if ("." == $file OR ".." == $file)
        {
          continue; // skip current directory and directory above
        }
        if(fnmatch($value . ".gif", $file) ||
        fnmatch($value . ".jpg", $file) ||
        fnmatch($value. ".png", $file))
        {
          $pdf->ezImage($upload_dir . "/" . $value . ".jpg", 0, 500, "none", "left");
        }
      }

      $pdf->ezText("");
    }

    $pdf->ezStream();
  }
  public  function pdfObjectnames($result)  // Creates a pdf document from an array of objects
  { global $instDir;
    $page=1;
    $i=0;
    while(list($key,$valueA)=each($result))
      $obs1[]=array($valueA['showname']);
    // Create pdf file
    $pdf=new Cezpdf('a4','landscape');
    $pdf->ezStartPageNumbers(450, 15, 10);
    $pdf->selectFont($instDir.'lib/fonts/Helvetica.afm');
    $pdf->ezText(html_entity_decode($_GET['pdfTitle']),18);
    $pdf->ezColumnsStart(array('num'=>10));
    $pdf->ezTable($obs1,
                  '', 
	                '',
                  array("width" => "750",
			                  "cols" => array(array('justification'=>'left', 'width'=>80)),
											  "fontSize" => "7",
											  "showLines" => "0",
											  "showHeadings" => "0",
											  "rowGap" => "0",
											  "colGap" => "0"				         
											 )
								 );
		$pdf->ezStream();
  }
  public  function pdfObjects($result)  // Creates a pdf document from an array of objects
  { global $instDir, $objAtlas, $objObserver,$objPresentations, $loggedUser;
    while(list($key,$valueA)=each($result))
      $obs1[]=array("Name"          => $valueA['showname'],
                    "ra"            => $objPresentations->raToString($valueA['objectra']),
                    "decl"          => $objPresentations->decToString($valueA['objectdecl'], 0),
                    "mag"           => $objPresentations->presentationInt1($valueA['objectmagnitude'],99.9,''),
                    "sb"            => $objPresentations->presentationInt1($valueA['objectsurfacebrightness'],99.9,''),
                    "con"           => $GLOBALS[$valueA['objectconstellation']],
                    "diam"          => $valueA['objectsize'],
                    "pa"            => $objPresentations->presentationInt($valueA['objectpa'],999,"-"), 
                    "type"          => $GLOBALS[$valueA['objecttype']],
                    "page"          => $valueA[$objObserver->getObserverProperty($this->checkSessionKey('deepskylog_id',''),'standardAtlasCode','urano')],
                    "contrast"      => $valueA['objectcontrast'],
                    "magnification" => $valueA['objectoptimalmagnificationvalue'],
                    "seen"          => $valueA['objectseen'],
  	                "seendate"      => $valueA['objectlastseen']
                   );
    $pdf = new Cezpdf('a4', 'landscape');
    $pdf->ezStartPageNumbers(450, 15, 10);
    $fontdir = $instDir.'lib/fonts/Helvetica.afm';
    $pdf->selectFont($fontdir); 
    $pdf->ezTable($obs1,
                  array("Name"          => html_entity_decode(LangPDFMessage1),
                        "ra"            => html_entity_decode(LangPDFMessage3),
                        "decl"          => html_entity_decode(LangPDFMessage4),
                        "type"          => html_entity_decode(LangPDFMessage5),
                        "con"           => html_entity_decode(LangPDFMessage6),
                        "mag"           => html_entity_decode(LangPDFMessage7),
                        "sb"            => html_entity_decode(LangPDFMessage8),
                        "diam"          => html_entity_decode(LangPDFMessage9),
                        "pa"            => html_entity_decode(LangPDFMessage16),  
                        "page"          => html_entity_decode($objAtlas->atlasCodes[$objObserver->getObserverProperty($loggedUser,'standardAtlasCode','urano')]),
                        "contrast"      => html_entity_decode(LangPDFMessage17),
                        "magnification" => html_entity_decode(LangPDFMessage18),
                        "seen"          => html_entity_decode(LangOverviewObjectsHeader7),
                        "seendate"      => html_entity_decode(LangOverviewObjectsHeader8)
                       ),
                  html_entity_decode($_GET['pdfTitle']),
                  array("width"=>"750",
			                  "cols"=>array("Name"          => array('justification'=>'left',  'width'=>100),
			                                "ra"            => array('justification'=>'center','width'=>65),
		              									  "decl"          => array('justification'=>'center','width'=>50),
									              		  "type"          => array('justification'=>'left',  'width'=>110),
              											  "con"           => array('justification'=>'left',  'width'=>90),
							              				  "mag"           => array('justification'=>'center','width'=>35),
              											  "sb"            => array('justification'=>'center','width'=>35),
							              			  	"diam"          => array('justification'=>'center','width'=>65),
       											          "pa"            => array('justification'=>'center','width'=>30),
				              							  "page"          => array('justification'=>'center','width'=>45),
          														"contrast"      => array('justification'=>'center','width'=>35),
          														"magnification" => array('justification'=>'center','width'=>35),
											                "seen"          => array('justification'=>'center','width'=>50),
											                "seendate"      => array('justification'=>'center','width'=>50)
                                     ),
									      "fontSize" => "7"				         
								       )
								 );
	$pdf->ezStream();
  }
  public  function pdfObjectsDetails($result, $sort='')  // Creates a pdf document from an array of objects
  { global $deepskylive,$dateformat,$baseURL,$instDir,$objObserver,$loggedUser,$objLocation,$objInstrument,$objPresentations;
    if($sort=='objectconstellation') $sort='con'; else $sort='';
	  $pdf = new Cezpdf('a4', 'landscape');
    $pdf->selectFont($instDir.'lib/fonts/Helvetica.afm');
    $actualsort='';$y = 0;$bottom = 40;$bottomsection = 30;$top = 550;$header = 570;
    $footer = 10;$xleft = 20;$xmid = 431;$fontSizeSection = 10;$fontSizeText = 8;
    $deltaline = $fontSizeText+4;$deltalineSection = 2;$pagenr = 0;$xbase = $xmid;
		$sectionBarHeight = $fontSizeSection + 4;$descriptionLeadingSpace = 20;$sectionBarSpace = 3;
		$SectionBarWidth = 400+$sectionBarSpace;$theDate=date('d/m/Y');
    $pdf->addTextWrap($xleft,$header,100,8,$theDate);
		if($loggedUser&&$objObserver->getObserverProperty($loggedUser,'name')
		&& $objLocation->getLocationPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdlocation'),'name')
		&& $objInstrument->getInstrumentPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdtelescope'),'name'))
      $pdf->addTextWrap($xleft, $footer, $xmid+$SectionBarWidth, 8, 
		    html_entity_decode(LangPDFMessage19 .$objObserver->getObserverProperty($loggedUser,'firstname') . ' ' . 
				                   $objObserver->getObserverProperty($loggedUser,'name') . ' ' .
		    LangPDFMessage20 . $objInstrument->getInstrumentPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdtelescope'),'name') . ' ' . 
				LangPDFMessage21 . $objLocation->getLocationPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdlocation'),'name')), 'center' );
		$pdf->addTextWrap($xleft, $header, $xmid+$SectionBarWidth, 10, html_entity_decode($_GET['pdfTitle']), 'center' );
		$pdf->addTextWrap($xmid+$SectionBarWidth-$sectionBarSpace-100, $header, 100, 8, LangPDFMessage22 . '1', 'right');
		while(list($key, $valueA) = each($result))
    { $con = $valueA['objectconstellation'];
    	if(!$sort || ($actualsort!=$$sort))
			{ if($y<$bottom) 
  			{ $y=$top;
  			  if($xbase==$xmid)
  				{ if($pagenr++) 
					  { $pdf->newPage();
						  $pdf->addTextWrap($xleft, $header, 100, 8, $theDate);
							if($loggedUser&&$objObserver->getObserverProperty($loggedUser,'name')
							&& $objLocation->getLocationPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdlocation'),'name')
							&& $objInstrument->getInstrumentPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdtelescope'),'name'))
						    $pdf->addTextWrap($xleft, $footer, $xmid+$SectionBarWidth, 8, 
		                   html_entity_decode(
		                   LangPDFMessage19 . $objObserver->getObserverProperty($loggedUser,'name') . ' ' . 
		                                      $objObserver->getObserverProperty($loggedUser,'firstname') . ' ' .
                       LangPDFMessage20 . $objInstrument->getInstrumentPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdtelescope'),'name') . ' ' . 
				               LangPDFMessage21 . $objLocation->getLocationPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdlocation'),'name')), 'center' );
		          $pdf->addTextWrap($xleft, $header, $xmid+$SectionBarWidth, 10, html_entity_decode($_GET['pdfTitle']), 'center' );
		          $pdf->addTextWrap($xmid+$SectionBarWidth-$sectionBarSpace-100, $header, 100, 8, LangPDFMessage22 . $pagenr, 'right');
  					}
						$xbase = $xleft;
  				}
  				else
  				{ $xbase = $xmid;
  				}
  			}
				if($sort)
				{ $y-=$deltalineSection;
          $pdf->rectangle($xbase-$sectionBarSpace, $y-$sectionBarSpace, $SectionBarWidth, $sectionBarHeight);
          $pdf->addText($xbase, $y, $fontSizeSection, $GLOBALS[$$sort]);  
          $y-=$deltaline+$deltalineSection;
				}
			}
      elseif($y<$bottomsection) 
			{ $y=$top;
			  if($xbase==$xmid)
				{ if($pagenr++) 
				  { $pdf->newPage();
					  $pdf->addTextWrap($xleft, $header, 100, 8, $theDate);
						if($loggedUser&&$objObserver->getObserverProperty($loggedUser,'name')
						&& $objLocation->getLocationPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdlocation'),'name')
						&& $objInstrument->getInstrumentPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdtelescope'),'name'))
					    $pdf->addTextWrap($xleft, $footer, $xmid+$SectionBarWidth, 8, 
	                   html_entity_decode(LangPDFMessage19 . $objObserver->getObserverProperty($loggedUser,'name') . ' ' .
	                                      $objObserver->getObserverProperty($loggedUser,'firstname') . ' ' .
                     LangPDFMessage20 . $objInstrument->getInstrumentPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdtelescope'),'name') . ' ' . 
			               LangPDFMessage21 . $objLocation->getLocationPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdlocation'),'name')), 'center' );
            $pdf->addTextWrap($xleft, $header, $xmid+$SectionBarWidth, 10, html_entity_decode($_GET['pdfTitle']), 'center' );
	          $pdf->addTextWrap($xmid+$SectionBarWidth-$sectionBarSpace-100, $header, 100, 8, LangPDFMessage22 . $pagenr, 'right');
					}
					$xbase = $xleft;
          if($sort)
					{ $y-=$deltalineSection;
            $pdf->rectangle($xbase-$sectionBarSpace, $y-$sectionBarSpace, $SectionBarWidth, $sectionBarHeight);
            $pdf->addText($xbase, $y, $fontSizeSection, $GLOBALS[$$sort]);
            $y-=$deltaline+$deltalineSection;
					}
				}
				else
				{ $xbase = $xmid;
          if($sort)
					{ $y-=$deltalineSection;
            $pdf->rectangle($xbase-$sectionBarSpace, $y-$sectionBarSpace, $SectionBarWidth, $sectionBarHeight);
					  $pdf->addText($xbase, $y, $fontSizeSection, $GLOBALS[$$sort]);
            $y-=$deltaline+$deltalineSection;
					}
				}
			}
			if(!$sort)
			{ $pdf->addTextWrap($xbase    , $y,  30, $fontSizeText, $valueA['objectseen']);			                   // seen
			  $pdf->addTextWrap($xbase+ 30, $y,  40, $fontSizeText, $valueA['objectlastseen']);		                     // last seen	
			  $pdf->addTextWrap($xbase+ 70, $y,  85, $fontSizeText, '<b>'.
				  '<c:alink:'.$baseURL.'index.php?indexAction=detail_object&amp;object='.
					urlencode($valueA['objectname']).'>'.$valueA['showname']);		               //	object
			  $pdf->addTextWrap($xbase+150, $y,  30, $fontSizeText, '</c:alink></b>'.$valueA['objecttype']);			                 // type
			  $pdf->addTextWrap($xbase+180, $y,  20, $fontSizeText, $valueA['objectconstellation']);			                         // constellation
			  $pdf->addTextWrap($xbase+200, $y,  17, $fontSizeText, $objPresentations->presentationInt1($valueA['objectmagnitude'],99.9,''), 'left');  	                 // mag
			  $pdf->addTextWrap($xbase+217, $y,  18, $fontSizeText, $objPresentations->presentationInt1($valueA['objectsurfacebrightness'],99.9,''), 'left');		                   // sb
			  $pdf->addTextWrap($xbase+235, $y,  60, $fontSizeText, $objPresentations->raToStringHM($valueA['objectra']) . ' '.
				                                                      $objPresentations->decToString($valueA['objectdecl'],0));	 // ra - decl
			  $pdf->addTextWrap($xbase+295, $y,  55, $fontSizeText, $valueA['objectsize'] . '/' . $objPresentations->presentationInt($valueA['objectpa'],999,"-"));			             // size
	  		$pdf->addTextWrap($xbase+351, $y,  17, $fontSizeText, $objPresentations->presentationInt1($valueA['objectcontrast'],'',''), 'left');			             // contrast				
	  		$pdf->addTextWrap($xbase+368, $y,  17, $fontSizeText, (int)$valueA['objectoptimalmagnification'], 'left');			             // magnification				
			  $pdf->addTextWrap($xbase+380, $y,  20, $fontSizeText, '<b>'.$valueA[($loggedUser?$objObserver->getObserverProperty($loggedUser,'standardAtlasCode','urano'):'urano')].'</b>', 'right');			   // atlas page
      }
      else
			{ $pdf->addTextWrap($xbase    , $y,  30, $fontSizeText, $valueA['objectseen']);			                   // seen
			  $pdf->addTextWrap($xbase+ 30, $y,  40, $fontSizeText, $valueA['objectlastseen']);		                     // last seen	
			  $pdf->addTextWrap($xbase+ 70, $y, 100, $fontSizeText, '<b>'.
				  '<c:alink:'.$baseURL.'index.php?indexAction=detail_object&amp;object='.
					urlencode($valueA['objectname']).'>'.$valueA['showname']);		                                       //	object
			  $pdf->addTextWrap($xbase+170, $y,  30, $fontSizeText, '</c:alink></b>'.$valueA['objecttype']);			                 // type
			  $pdf->addTextWrap($xbase+200, $y,  17, $fontSizeText, $objPresentations->presentationInt1($valueA['objectmagnitude'],99.9,''), 'left');			                 // mag
			  $pdf->addTextWrap($xbase+217, $y,  18, $fontSizeText, $objPresentations->presentationInt1($valueA['objectsurfacebrightness'],99.9,''), 'left');			                   // sb
			  $pdf->addTextWrap($xbase+235, $y,  60, $fontSizeText, $objPresentations->raToStringHM($valueA['objectra']) . ' '.
				                                                      $objPresentations->decToString($valueA['objectdecl'],0));	 // ra - decl
			  $pdf->addTextWrap($xbase+295, $y,  55, $fontSizeText, $valueA['objectsize'] . '/' . $objPresentations->presentationInt($valueA['objectpa'],999,"-"));         			   // size
	  		$pdf->addTextWrap($xbase+351, $y,  17, $fontSizeText, $objPresentations->presentationInt1($valueA['objectcontrast'],0,''), 'left');			             // contrast				
	  		$pdf->addTextWrap($xbase+368, $y,  17, $fontSizeText, $objPresentations->presentationInt((int)$valueA['objectoptimalmagnification'],0,''), 'left');		               // magnification				
			  $pdf->addTextWrap($xbase+380, $y,  20, $fontSizeText, '<b>'.$valueA[($loggedUser?$objObserver->getObserverProperty($loggedUser,'standardAtlasCode','urano'):'urano')].'</b>', 'right');			   // atlas page
      }
			$y-=$deltaline;
      if($sort)
			  $actualsort = $$sort;
			if(array_key_exists('objectlistdescription',$valueA) && $valueA['objectlistdescription'])
      { $theText= $objPresentations->br2nl($valueA['objectlistdescription']);
			  $theText= $pdf->addTextWrap($xbase+$descriptionLeadingSpace, $y, $xmid-$xleft-$descriptionLeadingSpace-10 ,$fontSizeText, '<i>'.$theText);
  			$y-=$deltaline;	
        while($theText)
				{ if($y<$bottomsection) 
			    { $y=$top;
			      if($xbase==$xmid)
				    { if($pagenr++)
						  { $pdf->newPage();
							  $pdf->addTextWrap($xleft, $header, 100, 8, $theDate);
								if($objObserver->getObserverProperty($loggedUser,'name')
								&& $objLocation->getLocationPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdlocation'),'name')
								&& $objInstrument->getInstrumentPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdtelescope'),'name'))
							    $pdf->addTextWrap($xleft, $footer, $xmid+$SectionBarWidth, 8, 
		                   html_entity_decode(LangPDFMessage19 . $objObserver->getObserverProperty($loggedUser,'name') . ' ' . 
		                                      $objObserver->getObserverProperty($loggedUser,'firstname') . 
                       LangPDFMessage20 . $objInstrument->getInstrumentPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdtelescope'),'name') . ' ' . 
				               LangPDFMessage21 . $objLocation->getLocationPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdlocation'),'name')), 'center' );
		            $pdf->addTextWrap($xleft, $header, $xmid+$SectionBarWidth, 10, html_entity_decode($_GET['pdfTitle']), 'center' );
		            $pdf->addTextWrap($xmid+$SectionBarWidth-$sectionBarSpace-100, $header, 100, 8, LangPDFMessage22 . $pagenr, 'right');
          	  }
						  $xbase = $xleft;
              if($sort)
							{ $y-=$deltalineSection;
                $pdf->rectangle($xbase-$sectionBarSpace, $y-$sectionBarSpace, $SectionBarWidth, $sectionBarHeight);
                $pdf->addText($xbase, $y, $fontSizeSection, $GLOBALS[$$sort]);
                $y-=$deltaline+$deltalineSection;
							}
				    }
				    else
				    { $xbase = $xmid;
              if($sort)
							{ $y-=$deltalineSection;
                $pdf->rectangle($xbase-$sectionBarSpace, $y-$sectionBarSpace, $SectionBarWidth, $sectionBarHeight);
					      $pdf->addText($xbase, $y, $fontSizeSection, $GLOBALS[$$sort]);
                $y-=$deltaline+$deltalineSection;
							}
				    }
			    }
				$theText= $pdf->addTextWrap($xbase+$descriptionLeadingSpace, $y, $xmid-$xleft-$descriptionLeadingSpace-10 ,$fontSizeText, $theText);
  			$y-=$deltaline;	
				}
			  $pdf->addText(0,0,10,'</i>');
			}
			elseif(array_key_exists('objectdescription',$valueA) && $valueA['objectdescription'])
      { $theText= $objPresentations->br2nl($valueA['objectdescription']);
			  $theText= $pdf->addTextWrap($xbase+$descriptionLeadingSpace, $y, $xmid-$xleft-$descriptionLeadingSpace-10 ,$fontSizeText, '<i>'.$theText);
  			$y-=$deltaline;	
        while($theText)
				{ if($y<$bottomsection) 
			    { $y=$top;
			      if($xbase==$xmid)
				    { if($pagenr++)
						  { $pdf->newPage();
							  $pdf->addTextWrap($xleft, $header, 100, 8, $theDate);
								if($objObserver->getObserverProperty($loggedUser,'name')
								&& $objLocation->getLocationPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdlocation'),'name')
								&& $objInstrument->getInstrumentPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdtelescope'),'name'))
							    $pdf->addTextWrap($xleft, $footer, $xmid+$SectionBarWidth, 8, 
		                   html_entity_decode(LangPDFMessage19 . $objObserver->getObserverProperty($loggedUser,'name') . ' ' . 
		                                      $objObserver->getObserverProperty($loggedUser,'firstname') . 
                       LangPDFMessage20 . $objInstrument->getInstrumentPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdtelescope'),'name') . ' ' . 
				               LangPDFMessage21 . $objLocation->getLocationPropertyFromId($objObserver->getObserverProperty($loggedUser,'stdlocation'),'name')), 'center' );
		            $pdf->addTextWrap($xleft, $header, $xmid+$SectionBarWidth, 10, html_entity_decode($_GET['pdfTitle']), 'center' );
		            $pdf->addTextWrap($xmid+$SectionBarWidth-$sectionBarSpace-100, $header, 100, 8, LangPDFMessage22 . $pagenr, 'right');
          	  }
						  $xbase = $xleft;
              if($sort)
							{ $y-=$deltalineSection;
                $pdf->rectangle($xbase-$sectionBarSpace, $y-$sectionBarSpace, $SectionBarWidth, $sectionBarHeight);
                $pdf->addText($xbase, $y, $fontSizeSection, $GLOBALS[$$sort]);
                $y-=$deltaline+$deltalineSection;
							}
				    }
				    else
				    { $xbase = $xmid;
              if($sort)
							{ $y-=$deltalineSection;
                $pdf->rectangle($xbase-$sectionBarSpace, $y-$sectionBarSpace, $SectionBarWidth, $sectionBarHeight);
					      $pdf->addText($xbase, $y, $fontSizeSection, $GLOBALS[$$sort]);
                $y-=$deltaline+$deltalineSection;
							}
				    }
			    }
				$theText= $pdf->addTextWrap($xbase+$descriptionLeadingSpace, $y, $xmid-$xleft-$descriptionLeadingSpace-10 ,$fontSizeText, $theText);
  			$y-=$deltaline;	
				}
			  $pdf->addText(0,0,10,'</i>');
			}			
		}		
    $pdf->Stream(); 
  }
  public  function pdfObservations($result) // Creates a pdf document from an array of observations
  { global $loggedUser, $deepskylive, $dateformat, $instDir, $objObservation, $objObserver, $objInstrument, $objLocation, $objPresentations, $objObject, $objFilter, $objEyepiece, $objLens;
    $pdf = new Cezpdf('a4', 'portrait');
    $pdf->ezStartPageNumbers(300, 30, 10);
    $pdf->selectFont($instDir.'lib/fonts/Helvetica.afm');
    $pdf->ezText(html_entity_decode($_GET['pdfTitle'])."\n");
    while(list($key,$value)=each($result))
    { $obs=$objObservation->getAllInfoDsObservation($value['observationid']);
      $object=$objObject->getAllInfoDsObject($obs['objectname']);
      if($loggedUser&&($objObserver->getObserverProperty($loggedUser,'UT')))
        $date=sscanf($obs["date"], "%4d%2d%2d");
      else
        $date=sscanf($obs["localdate"], "%4d%2d%2d");
      $formattedDate=date($dateformat,mktime(0,0,0,$date[1],$date[2],$date[0]));
      $temp = array("Name"        => html_entity_decode(LangPDFMessage1)." : ".$obs['objectname'],
                    "altname"     => html_entity_decode(LangPDFMessage2)." : ".$object["altname"],
                    "type"        => $GLOBALS[$object['type']].html_entity_decode(LangPDFMessage12).$GLOBALS[$object['con']],
                    "visibility"  => (($obs['visibility'])?(html_entity_decode(LangViewObservationField22)." : ".$GLOBALS['Visibility'.$obs['visibility']]):''),
                    "seeing"      => (($obs['seeing'])?(LangViewObservationField6." : ".$GLOBALS['Seeing'.$obs['seeing']]):''),
                    "limmag"      => (($obs['limmag'])?(LangViewObservationField7." : ".$obs['limmag']):''), 
                    "filter"      => (($obs['filterid'])?(LangViewObservationField31. " : " . $objFilter->getFilterPropertyFromId($obs['filterid'],'name')):''),
                    "eyepiece"    => (($obs['eyepieceid'])?(LangViewObservationField30. " : " .$objEyepiece->getEyepiecePropertyFromId($obs['eyepieceid'],'name')):''),
								    "lens"        => (($obs['lensid'])?(LangViewObservationField32 . " : " . $objLens->getLensPropertyFromId($obs['lensid'],'name')):''),
                    "observer"    => html_entity_decode(LangPDFMessage13).$objObserver->getObserverProperty($obs['observerid'],'firstname')." ".$objObserver->getObserverProperty($obs['observerid'],'name').html_entity_decode(LangPDFMessage14).$formattedDate,
                    "instrument"  => html_entity_decode(LangPDFMessage11)." : ".$objInstrument->getInstrumentPropertyFromId($obs['instrumentid'],'name'),
                    "location"    => html_entity_decode(LangPDFMessage10)." : ".$objLocation->getLocationPropertyFromId($obs['locationid'],'name'),
                    "description" => $objPresentations->br2nl(html_entity_decode($obs['description'])),
                    "desc"        => html_entity_decode(LangPDFMessage15)
                   );
      $obs1[] = $temp;
      $nm=$obs['objectname'];
      if($object["altname"])
        $nm=$nm." (".$object["altname"].")";
      $pdf->ezText($nm, "14");
      $pdf->ezTable($tmp=array(array("type"=>$temp["type"])),array("type" => html_entity_decode(LangPDFMessage5)),"", array("width" => "500", "showHeadings" => "0", "showLines" => "0", "shaded" => "0"));
      $pdf->ezTable($tmp=array(array("location"=>$temp["location"], "instrument"=>$temp["instrument"])), array("location" => html_entity_decode(LangPDFMessage1), "instrument" => html_entity_decode(LangPDFMessage2)), "",  array("width" => "500", "showHeadings" => "0", "showLines" => "0", "shaded" => "0"));
      if($obs['eyepieceid']) $pdf->ezTable($tmp=array(array("eyepiece"=>$temp["eyepiece"])),     array("eyepiece" => "test"),   "", array("width" => "500", "showHeadings" => "0", "showLines" => "0", "shaded" => "0"));
      if($obs['filterid'])   $pdf->ezTable($tmp=array(array("filter"=>$temp["filter"])),         array("filter" => "test"),     "", array("width" => "500", "showHeadings" => "0", "showLines" => "0", "shaded" => "0"));
      if($obs['lensid'])     $pdf->ezTable($tmp=array(array("lens"=>$temp["lens"])),             array("lens" => "test"),       "", array("width" => "500", "showHeadings" => "0", "showLines" => "0", "shaded" => "0"));
      if($obs['seeing'])     $pdf->ezTable($tmp=array(array("seeing"=>$temp["seeing"])),         array("seeing" => "test"),     "", array("width" => "500", "showHeadings" => "0", "showLines" => "0", "shaded" => "0"));
      if($obs['limmag'])     $pdf->ezTable($tmp=array(array("limmag"=>$temp["limmag"])),         array("limmag" => "test"),     "", array("width" => "500", "showHeadings" => "0", "showLines" => "0", "shaded" => "0"));
      if($obs['visibility']) $pdf->ezTable($tmp=array(array("visibility"=>$temp["visibility"])), array("visibility" => "test"), "", array("width" => "500", "showHeadings" => "0", "showLines" => "0", "shaded" => "0"));
      $pdf->ezTable($tmp=array(array("observer"=>$temp["observer"])), array("observer" => html_entity_decode(LangPDFMessage1)), "", array("width" => "500", "showHeadings" => "0", "showLines" => "0", "shaded" => "0"));
         $pdf->ezText(LangPDFMessage15, "12");
      $pdf->ezText("");
      $pdf->ezTable($tmp=array(array("description"=>$temp["description"])), array("description" => html_entity_decode(LangPDFMessage1)), "", array("width" => "500", "showHeadings" => "0", "showLines" => "0", "shaded" => "0"));
      if($objObservation->getDsObservationProperty($value['observationid'],'hasDrawing'))
      { $pdf->ezText("");
        $pdf->ezImage($instDir."deepsky/drawings/".$value['observationid'].".jpg", 0, 500, "none", "left");
      }
      $pdf->ezText("");
      $pdf->ezNewPage();
    }
    $pdf->ezStream();
  }  
  public function printNewListHeader3(&$list, $link, $min, $step, $total=0,$showNumberOfRecords=true,$showArrows=true)
  { global $baseURL;
	  $pages=ceil(count($list)/$step);           // total number of pages
    if($min)                                   // minimum value
    { $min=$min-($min%$step);                  // start display from number of $steps
      if ($min < 0)                            // minimum value smaller than 0
        $min=0;
      if($min>count($list))                    // minimum value bigger than number of elements
        $min=count($list)-(count($list)%$step);
    }
    else                                       // no minimum value defined
      $min=0;
    $max=$min+$step;                       // maximum number to be displayed
    $content="<form action=\"".$link."\" method=\"post\">";
    $content.="<div>";
    if($showNumberOfRecords)
      $content.= "(".($listcount=count($list))."&nbsp;".(($listcount==1)?LangNumberOfRecords1:LangNumberOfRecords).(($total&&($total!=count($list)))?" / ".$total:"").(($pages>1)?(" ".LangNumberOfRecordsIn." ".$pages." ".LangNumberOfRecords1Pages.")"):")")."&nbsp;";
    if(($listcount>$step)&&($showArrows))
    { $currentpage=ceil($min/$step)+1;
			$content.= "<a href=\"".$link."&amp;multiplepagenr=0\">"."<img class=\"navigationButton\" src=\"".$baseURL."styles/images/allleft20.gif\" alt=\"&lt;&lt;0\" />"."</a>";
		  $content.= "<a href=\"".$link."&amp;multiplepagenr=".($currentpage>0?($currentpage-1):$currentpage)."\">"."<img class=\"navigationButton\" src=\"".$baseURL."styles/images/left20.gif\" alt=\"&lt;\" />"."</a>";			
		  $content.= "<input type=\"text\" name=\"multiplepagenr\" size=\"3\" class=\"inputfield centered\" value=\"".$currentpage."\" />";	
		  $content.= "<a href=\"".$link."&amp;multiplepagenr=".($currentpage<$pages?($currentpage+1):$currentpage)."\">"."<img class=\"navigationButton\" src=\"".$baseURL."styles/images/right20.gif\" alt=\"&gt;\" />"."</a>";
		  $content.= "<a href=\"".$link."&amp;multiplepagenr=".$pages."\">"."<img class=\"navigationButton\" src=\"".$baseURL."styles/images/allright20.gif\" alt=\"&gt;&gt;\" />"."</a>";
		  $content.= "&nbsp;";
	  }
	  $content.="</div>";
	  $content.= "</form>";
	  return array($min,$max,$content);
  }
  public function printStepsPerPage3($link,$detaillink,$steps=25)
  { global $baseURL;
    $content =LangNumberOfRecordsPerPages.": ";
    $content.="<a href=\"#\" onclick=\"theanswer=prompt('".addslashes(LangAskForDesiredNumberOfItemsPerPage)."','".addslashes($steps)."'); location.href='".$link."&amp;stepsCommand=".$detaillink."&amp;stepsValue='+theanswer; return false;\"	title=\"".LangCaptionAskForDesiredNumberOfItemsPerPage."\" rel=\"external\">".$steps."</a>";    
    //$content.="<input name=\"stepsPerPage".$detaillink."\" id=\"stepsPerPage".$detaillink."\" class=\"centered\" value=\"".$steps."\" size=\"3\" onchange=\"location='".$link."&amp;stepsCommand=".$detaillink."&amp;stepsValue='+this.value;\" />";
    return $content;
  }
  public function rssObservations()  // Creates an rss feed for DeepskyLog
	{
	  global $objObservation, $objInstrument, $objLocation, $objPresentations, $objObserver, $baseURL;
	  $dom = new DomDocument('1.0', 'US-ASCII');
	
	  // add root fcga -> The header
	  $rssInfo = $dom->createElement('rss');
	  $rssDom = $dom->appendChild($rssInfo);
	
	  $attr = $dom->createAttribute("version");
	  $rssInfo->appendChild($attr);
	
	  $attrText = $dom->createTextNode("2.0");
	  $attr->appendChild($attrText);
	
	  $attr = $dom->createAttribute("xmlns:content");
	  $rssInfo->appendChild($attr);
	
	  $attrText = $dom->createTextNode("http://purl.org/rss/1.0/modules/content/");
	  $attr->appendChild($attrText);
	
	  $attr = $dom->createAttribute("xmlns:dc");
	  $rssInfo->appendChild($attr);
	
	  $attrText = $dom->createTextNode("http://purl.org/dc/elements/1.1/");
	  $attr->appendChild($attrText);
	   
	  $attr = $dom->createAttribute("xmlns:atom");
	  $rssInfo->appendChild($attr);
	
	  $attrText = $dom->createTextNode("http://www.w3.org/2005/Atom");
	  $attr->appendChild($attrText);
	
	  //add root - <channel>
	  $channelDom = $rssDom->appendChild($dom->createElement('channel'));
	
	  //add root - <channel> - <title>
	  $titleDom = $channelDom->appendChild($dom->createElement('title'));
	  $titleDom->appendChild($dom->createTextNode("DeepskyLog"));
	
	  //add root - <channel> - <description>
	  $descDom = $channelDom->appendChild($dom->createElement('description'));
	  $descDom->appendChild($dom->createTextNode("DeepskyLog - visual deepsky and comets observations"));
	
	  // add root - <channel> - <atom>
	  $atomDom = $channelDom->appendChild($dom->createElement('atom:link'));
	
	  $attr = $dom->createAttribute("href");
	  $atomDom->appendChild($attr);
	  
	  $attrText = $dom->createTextNode($baseURL . "observations.rss");
	  $attr->appendChild($attrText);
	
	  $attr = $dom->createAttribute("rel");
	  $atomDom->appendChild($attr);
	
	  $attrText = $dom->createTextNode("self");
	  $attr->appendChild($attrText);
	
	  $attr = $dom->createAttribute("type");
	  $atomDom->appendChild($attr);
	
	  $attrText = $dom->createTextNode("application/rss+xml");
	  $attr->appendChild($attrText);
	  
	  //add root - <channel> - <link>
	  $linkDom = $channelDom->appendChild($dom->createElement('link'));
	  $linkDom->appendChild($dom->createTextNode("http://www.deepskylog.org/"));
	
	  $theDate = date('r');
	
	  //add root - <channel> - <link>
	  $lbdDom = $channelDom->appendChild($dom->createElement('lastBuildDate'));
	  $lbdDom->appendChild($dom->createTextNode($theDate));
	
	  // Get the new deepsky observations of the last month
	  $theDate = date('Ymd', strtotime('-1 month'));
	
	  $_GET['minyear'] = substr($theDate,0,4);
	
	  $_GET['minmonth'] = substr($theDate,4,2);
	
	  $_GET['minday'] = substr($theDate,6,2);
	  
	  $query = array("catalog"=>'%',"mindate"=>$GLOBALS['objUtil']->checkGetDate('minyear', 'minmonth','minday'));
	
	  $result = $objObservation->getObservationFromQuery($query, 'D');
	
	  while (list($key,$value)=each($result)) {
	    //add root - <channel> - <item>
	    $itemDom = $channelDom->appendChild($dom->createElement('item'));
	
	    $titleDom = $itemDom->appendChild($dom->createElement('title'));
	    $titleDom->appendChild($dom->createTextNode($value['objectname'] . " with "
	     . htmlspecialchars_decode($objInstrument->getInstrumentPropertyFromId($value['instrumentid'],'name')) . " from "
	     . $objLocation->getLocationPropertyFromId($objObservation->getDsObservationProperty($value['observationid'],'locationid'), 'name')));
	    $linkDom = $itemDom->appendChild($dom->createElement('link'));
	    $linkDom->appendChild($dom->createCDATASection($baseURL . "index.php?indexAction=detail_observation&observation=" . $value['observationid'] . "&QobsKey=0&dalm=D"));
	
	    $descDom = $itemDom->appendChild($dom->createElement('description'));
	    $descDom->appendChild($dom->createCDATASection($objPresentations->br2nl(utf8_encode($value['observationdescription']))));
	    
	    $authorDom = $itemDom->appendChild($dom->createElement('dc:creator'));
	    $authorDom->appendChild($dom->createCDATASection($value['observername']));
	
	    $guidDom = $itemDom->appendChild($dom->createElement('guid'));
	    $guidDom->appendChild($dom->createTextNode("deepsky".$value['observationid']));
	    
	    $attr = $dom->createAttribute("isPermaLink");
	    $guidDom->appendChild($attr);
	    
	    $attrText = $dom->createTextNode("false");
	    $attr->appendChild($attrText);
	    
	    $pubDateDom = $itemDom->appendChild($dom->createElement('pubDate'));
	    
	    date_default_timezone_set('UTC');
	
	    $time = -999;
	    
      $obs=$objObservation->getAllInfoDsObservation($value['observationid']);
      $time=$obs['time'];

      if($time>="0")
      { $hour=(int)($time/100);
        $minute=$time-(100*$hour);
	    } else {
	      $hour = 0;
	      $minute = 0;
	    }
	    $date = $value['observationdate'];
	
	    $year = substr($date,0,4);
	    $month = substr($date,4,2);
	    $day = substr($date,6,2);
	    
	    $pubDateDom->appendChild($dom->createTextNode(date("r", mktime($hour, $minute, 0, $month, $day, $year))));
	  }

    include_once "cometobjects.php";
    include_once "observers.php";
    include_once "instruments.php";
    include_once "locations.php";
    include_once "cometobservations.php";
    include_once "icqmethod.php";
    include_once "icqreferencekey.php";
    global $instDir,$objCometObject;
    $objects = new CometObjects;
    $observer = new Observers;
    $instrument = new Instruments;
    $observation = new CometObservations;
    $location = new Locations;
    $util = $this;
    $ICQMETHODS = new ICQMETHOD();
    $ICQREFERENCEKEYS = new ICQREFERENCEKEY();

    $cometsResult = $observation->getObservationFromQuery($query);
  
    while(list ($key, $value) = each($cometsResult))
    {
      $objectname = $objCometObject->getName($observation->getObjectId($value));
      
      //add root - <channel> - <item>
      $itemDom = $channelDom->appendChild($dom->createElement('item'));

      $title = htmlspecialchars_decode($objectname);
      
      // Location and instrument
      if ($observation->getLocationId($value) != 0 && $observation->getLocationId($value) != 1)
      {
        $title = $title . " from " . htmlspecialchars_decode($location->getLocationPropertyFromId($observation->getLocationId($value),'name'));
      }
      
      if ($observation->getInstrumentId($value) != 0)
      {
        $title = $title . " with " . htmlspecialchars_decode($instrument->getInstrumentPropertyFromId($observation->getInstrumentId($value),'name'));
      }
      
      $titleDom = $itemDom->appendChild($dom->createElement('title'));
      $titleDom->appendChild($dom->createTextNode($title));
      $linkDom = $itemDom->appendChild($dom->createElement('link'));
      $linkDom->appendChild($dom->createCDATASection($baseURL . "index.php?indexAction=comets_detail_observation&observation=" . $value));
  
      // Description
      $description = $observation->getDescription($value);

      if (strcmp($description, "") != 0)
      {
        $descDom = $itemDom->appendChild($dom->createElement('description'));
        $descDom->appendChild($dom->createCDATASection($objPresentations->br2nl(utf8_encode($description))));
      } else {
        $descDom = $itemDom->appendChild($dom->createElement('description'));
        $descDom->appendChild($dom->createCDATASection(""));
      }

      $observerid = $observation->getObserverId($value);
      $observername = $observer->getObserverProperty($observerid,'firstname')." ".$observer->getObserverProperty($observerid,'name');

      $authorDom = $itemDom->appendChild($dom->createElement('dc:creator'));
      $authorDom->appendChild($dom->createCDATASection($observername));
  
      $guidDom = $itemDom->appendChild($dom->createElement('guid'));
      $guidDom->appendChild($dom->createTextNode("comet".$value));
      
      $attr = $dom->createAttribute("isPermaLink");
      $guidDom->appendChild($attr);
      
      $attrText = $dom->createTextNode("false");
      $attr->appendChild($attrText);
      
      $pubDateDom = $itemDom->appendChild($dom->createElement('pubDate'));
      
      date_default_timezone_set('UTC');
  
      $date = sscanf($observation->getLocalDate($value), "%4d%2d%2d");
      $time = $observation->getLocalTime($value);

      $hour = (int)($time / 100);
      $minute = $time - $hour * 100;
      
      $pubDateDom->appendChild($dom->createTextNode(date("r", mktime($hour, $minute, 0, $date[1], $date[2], $date[0]))));
    }   

    //generate xml
	  $dom->formatOutput = true; // set the formatOutput attribute of
	  // domDocument to true
	  // save XML as string or file
	  $test1 = $dom->saveXML(); // put string in test1
	
	  print $test1;
	}
  private function utilitiesCheckIndexActionAdmin($action, $includefile)
  { if(array_key_exists('indexAction',$_REQUEST) && ($_REQUEST['indexAction'] == $action) && array_key_exists('admin', $_SESSION) && ($_SESSION['admin'] == "yes"))
      return $includefile; 
  }
  private function utilitiesCheckIndexActionAll($action, $includefile)
  { if(array_key_exists('indexAction',$_GET)&&($_GET['indexAction']==$action))
      return $includefile;
  }
  private function utilitiesCheckIndexActionDSquickPick()
  { global $objObject;
    if($this->checkGetKey('indexAction')=='quickpick')
    { if($this->checkGetKey('object'))
	    { if($temp=$objObject->getExactDsObject($_GET['object']))
	      { $_GET['object']=$temp;
					if(array_key_exists('searchObservationsQuickPick', $_GET))
	          return 'deepsky/content/selected_observations2.php';  
	        elseif(array_key_exists('newObservationQuickPick', $_GET))
	          return 'deepsky/content/new_observation.php';   
	        else
	          return 'deepsky/content/view_object.php';  
	      }
	      else
	      { $_GET['object']=ucwords(trim($_GET['object']));
	        if(array_key_exists('searchObservationsQuickPick', $_GET))
	          return 'deepsky/content/selected_observations2.php';  
	        elseif(array_key_exists('newObservationQuickPick', $_GET))
	          return 'deepsky/content/setup_objects_query.php';   
	        else
	          return 'deepsky/content/setup_objects_query.php';  
	      }
	    }
      else
      {	if(array_key_exists('searchObservationsQuickPick',$_GET))
	        return 'deepsky/content/setup_observations_query.php';  
	      elseif(array_key_exists('newObservationQuickPick',$_GET))
	        return 'deepsky/content/new_observation.php';   
	      else
	        return 'deepsky/content/setup_objects_query.php';  
       }
    }
  }
  public  function utilitiesDispatchIndexAction()
  { if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('add_csv'                            ,'deepsky/content/new_observationcsv.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('add_xml'                            ,'deepsky/content/new_observationxml.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('add_object'                         ,'deepsky/content/new_object.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('add_observation'                    ,'deepsky/content/new_observation.php'))) 
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('detail_object'                      ,'deepsky/content/view_object.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('detail_observation'                 ,'deepsky/content/view_observation.php'))) 
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('import_csv_list'                    ,'deepsky/content/new_listdatacsv.php')))  
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('listaction'                         ,'deepsky/content/tolist.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAdmin ('manage_csv_object'                  ,'deepsky/content/manage_objects_csv.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('query_objects'                      ,'deepsky/content/setup_objects_query.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('query_observations'                 ,'deepsky/content/setup_observations_query.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('rank_objects'                       ,'deepsky/content/top_objects.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('rank_observers'                     ,'deepsky/content/top_observers.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('result_query_objects'               ,'deepsky/content/execute_query_objects.php'))) 
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('result_selected_observations'       ,'deepsky/content/selected_observations2.php')))  
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('view_image'                         ,'deepsky/content/show_image.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('view_observer_catalog'              ,'deepsky/content/details_observer_catalog.php')))
    
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('change_account'                     ,'common/content/change_account.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('adapt_eyepiece'                     ,'common/content/change_eyepiece.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('adapt_filter'                       ,'common/content/change_filter.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('adapt_instrument'                   ,'common/content/change_instrument.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('adapt_lens'                         ,'common/content/change_lens.php')))	  
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('adapt_site'                         ,'common/content/change_site.php')))		
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('add_eyepiece'                       ,'common/content/new_eyepiece.php')))		 
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('add_filter'                         ,'common/content/new_filter.php')))		
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('add_instrument'                     ,'common/content/new_instrument.php'))) 		
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('add_lens'                           ,'common/content/new_lens.php')))		
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('add_site'                           ,'common/content/new_site.php'))) 		
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('detail_eyepiece'                    ,'common/content/change_eyepiece.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('detail_filter'                      ,'common/content/change_filter.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('detail_instrument'                  ,'common/content/change_instrument.php')))		
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('detail_lens'                        ,'common/content/change_lens.php')))		
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('detail_location'                    ,'common/content/change_site.php')))		
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('detail_observer'                    ,'common/content/view_observer.php')))		
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('message'                            ,'common/content/message.php')))		
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('search_sites'                       ,'common/content/search_locations.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('site_result'                        ,'common/content/getLocation.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('subscribe'                          ,'common/content/register.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('validate_lens'                      ,'common/control/validate_lens.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('view_eyepieces'                     ,'common/content/overview_eyepieces.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('view_filters'                       ,'common/content/overview_filters.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('view_instruments'                   ,'common/content/overview_instruments.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('view_lenses'                        ,'common/content/overview_lenses.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('view_locations'                     ,'common/content/overview_locations.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('view_observers'                     ,'common/content/overview_observers.php')))
    
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_all_observations'            ,'comets/content/overview_observations.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_detail_object'               ,'comets/content/view_object.php'))) 
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_detail_observation'          ,'comets/content/view_observation.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('comets_adapt_observation'           ,'comets/content/new_observation.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('comets_add_observation'             ,'comets/content/new_observation.php')))   
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_result_query_observations'   ,'comets/content/selected_observations.php')))   
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_detail_observation'          ,'comets/content/view_observation.php')))   
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionMember('comets_add_object'                  ,'comets/content/new_object.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_detail_object'               ,'comets/content/view_object.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_view_objects'                ,'comets/content/overview_objects.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_all_observations'            ,'comets/content/overview_observations.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_result_query_objects'        ,'comets/content/execute_query_objects.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_result_selected_observations','comets/content/selected_observations2.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_rank_observers'              ,'comets/content/top_observers.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_rank_objects'                ,'comets/content/top_objects.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionAll   ('comets_query_observations'          ,'comets/content/setup_observations_query.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionall   ('comets_query_objects'               ,'comets/content/setup_objects_query.php')))
    if(!($indexActionInclude=$this->utilitiesCheckIndexActionDSquickPick()))
      $indexActionInclude=$this->utilitiesGetIndexActionDefaultAction();
    return $indexActionInclude;
  }
  private function utilitiesGetIndexActionDefaultAction()
  { if($_SESSION['module']=='deepsky')
	  { $_GET['catalog']='%';
  	  $theDate = date('Ymd', strtotime('-1 year'));
      $_GET['minyear'] = substr($theDate,0,4);
      $_GET['minmonth'] = substr($theDate,4,2);
      $_GET['minday'] = substr($theDate,6,2);  
  	  return 'deepsky/content/selected_observations2.php';
		}
		else
		  return 'comets/content/overview_observations.php';	
  }
  private function utilitiesCheckIndexActionMember($action, $includefile)
  { global $loggedUser;
    if(array_key_exists('indexAction',$_GET) && ($_GET['indexAction'] == $action) && $loggedUser)
      return $includefile; 
  }
  public  function utilitiesSetModuleCookie($module)
  { if((!array_key_exists('module',$_SESSION)) ||
     (array_key_exists('module',$_SESSION) && ($_SESSION['module'] != $module)))
    { $_SESSION['module'] = $module;
      $cookietime = time() + 365 * 24 * 60 * 60;     // 1 year
      setcookie("module",$module, $cookietime, "/");
    }
  }
}
$objUtil=new Utils;
?>
