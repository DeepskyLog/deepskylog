<?php // loginuser.php - checks if the user is logged in based on cookie
if((!isset($inIndex))||(!$inIndex)) include "/redirect.php";
else
{ $loggedUser='';
  $loggedUserName='';
  $_SESSION['admin']="no";
  $loginErrorCode="";
  $loginErrorText="";
  if($objUtil->checkGetKey('indexAction')=='logout')
  { $_SESSION['deepskylog_id']='';
  	setcookie("deepskylogsec","",time()-3600,"/");
  	$loggedUser="";
    $_GET['indexAction']='default_action';
  }
  elseif(array_key_exists('deepskylogsec',$_COOKIE)&&$_COOKIE['deepskylogsec'])
  { if(strlen($_COOKIE['deepskylogsec'])>32)
    { if(substr($_COOKIE['deepskylogsec'],0,32)==$objObserver->getObserverProperty(substr($_COOKIE['deepskylogsec'],32,255),'password'))
      { $_SESSION['deepskylog_id']=substr($_COOKIE['deepskylogsec'],32,255);
  		  $_SESSION['lang']=$objUtil->checkPostKey('language',$objObserver->getObserverProperty($_SESSION['deepskylog_id'],'language'));
        $loggedUser=$_SESSION['deepskylog_id'];
  		  if($objObserver->getObserverProperty($_SESSION['deepskylog_id'],'role',2)=="0")                // administrator logs in 
          $_SESSION['admin']="yes";
  	  }
  		else
  		{ $loginErrorText="Wrong password cookie"; 																					
  		  $_GET['indexAction']='error_action';
  		}
    }
  	else
  	{ $loginErrorText="Wrong password cookie"; 																					
  	  $_GET['indexAction']='error_action';
  	}
  }
  elseif(array_key_exists('indexAction',$_GET)&&($_GET['indexAction']=='check_login'))                                                        // entered password
  { if(array_key_exists('deepskylog_id', $_POST)&&$_POST['deepskylog_id']&&array_key_exists('passwd', $_POST)&&$_POST['passwd'])              // all fields filled in
    { $login  = $_POST['deepskylog_id'];                                          // get password from form and encrypt
  	  $passwd = md5($_POST['passwd']);
  	  $passwd_db = $objObserver->getObserverProperty($login,'password');                  // get password from database 
      if($passwd_db==$passwd)                                                     // check if passwords match
      { $_SESSION['lang']=$objUtil->checkPostKey('language',$objObserver->getObserverProperty($login,'language'));
        if($objObserver->getObserverProperty($login,'role',2)=="2")                         // user in waitlist already tries to log in
  			{ $loginErrorCode="LangWelcome5";
  			  $loggedUser="";
  			} 
        elseif($objObserver->getObserverProperty($login,'role',2)=="1")                     // validated user
        { session_regenerate_id(true);
  			  $_SESSION['deepskylog_id']=$login;                                      // set session variable
          $_SESSION['admin']="no";                                                // set session variable
          $loggedUser=$_SESSION['deepskylog_id'];
          $cookietime=time()+(365*24*60*60);                                      // 1 year	      
  				setcookie("deepskylogsec",$passwd.$login,$cookietime,"/");
  	    }
        else                                                                      // administrator logs in 
        { session_regenerate_id(true);
        	$_SESSION['deepskylog_id']=$login;                              
          $_SESSION['admin']="yes";                           
          $loggedUser=$login;
          $cookietime=time()+(365*24*60*60);                                      // 1 year
          setcookie("deepskylogsec",$passwd.$login,$cookietime,"/");
        }
        unset($_SESSION['QobjParams']);
        $_GET['indexAction']='default_action';
      }
      else // passwords don't match
      { $loginErrorCode="LangErrorWrongPassword";
  		  $_GET['indexAction']='error_action';
  		  $loggedUser="";
  		}
    }
    else // not all fields are filled in
    { $loginErrorCode="LangErrorEmptyPassword";
  		$_GET['indexAction']='error_action';
  	}
  }
  else
  {	$_SESSION['deepskylog_id']='';
  	setcookie("deepskylogsec","",time()-3600,"/");
  	$loggedUser="";
  }
  if(((!array_key_exists('module',$_SESSION))||(!$_SESSION['module']))&&isset($_COOKIE['module']))
  {	$_SESSION['module']=$_COOKIE['module'];
    $objUtil->utilitiesSetModuleCookie($_SESSION['module']);
  }
  elseif((!array_key_exists('module',$_SESSION))||(!$_SESSION['module']))
  { $_SESSION['module']=$modules[0];
    $objUtil->utilitiesSetModuleCookie($_SESSION['module']);
  }
  if(!in_array($_SESSION['module'],$modules))
  { $_SESSION['module']=$modules[0];
    $objUtil->utilitiesSetModuleCookie($_SESSION['module']);
  }
  if(!array_key_exists('lang',$_SESSION))
    $_SESSION['lang']=$defaultLanguage;
  if(array_key_exists('indexAction',$_GET)&&($_GET['indexAction']=="setLanguage"))
  { if(array_key_exists('language',$_POST)&&$_POST['language']&&array_key_exists($_POST['language'],$objLanguage->getLanguages()))
      $_SESSION['lang']=$_POST['language'];
  	$_GET['indexAction']='default_action';
  }
  $language=$objLanguage->getPath($_SESSION['lang']);
  if($loggedUser)
  { $allLanguages=$objLanguage->getAllLanguages($objObserver->getObserverProperty($loggedUser,'language'));
    $_SESSION['alllanguages']=$allLanguages; 
    $usedLanguages=$objObserver->getUsedLanguages($loggedUser);
  }
  else
  { $allLanguages=$objLanguage->getAllLanguages($_SESSION['lang']);
    $_SESSION['alllanguages'] = $allLanguages; 
    $usedLanguages = $objLanguage->getLanguageKeys($_SESSION['lang']);
  }
  require_once "lib/setup/"."$language";
  if($loginErrorCode||$loginErrorText)
  { $_SESSION['deepskylog_id']='';
  	setcookie("deepskylogsec","",time()-3600,"/");
    $entryMessage=constant($loginErrorCode)." ".$loginErrorText;
  }
  if($loggedUser)
    $loggedUserName=$objObserver->getObserverProperty($loggedUser,'firstname')."&nbsp;".$objObserver->getObserverProperty($loggedUser,'name');
}
$objDatabase->execSQL("INSERT INTO logging(loginid, logdate, logtime, logurl, navigator, screenresolution, language) 
                       VALUES(\"".($loggedUser?$loggedUser:"anonymous")."\", ".
                                date('Ymd').", ".date('His').", '".addslashes(substr($objUtil->checkArrayKey($_SERVER,'REQUEST_URI',''),0,255))."', '".addslashes(substr($objUtil->checkArrayKey($_SERVER,'HTTP_USER_AGENT',''),0,255))."', 'screenres', '".$_SESSION['lang']."');");
$theDate = date('Ymd', strtotime('-1 year'));
if(($objUtil->checkGetKey('indexAction','x')=='x')||
   (($objUtil->checkGetKey('catalog','x')=='%')&&
    ($objUtil->checkGetKey('minyear','x')==substr($theDate,0,4))&&
    ($objUtil->checkGetKey('minmonth','x')==substr($theDate,4,2))&&
    ($objUtil->checkGetKey('minday','x')==substr($theDate,6,2))&&
    (($objUtil->checkGetKey('sort','x')=='x')||
     (($objUtil->checkGetKey('sort','x')=='observationid')&&
      ($objUtil->checkGetKey('sortdirection','x')=='desc')))))                                
  $lastReadObservation=($loggedUser?$objObserver->getLastReadObservation($loggedUser):-1);
else
  $lastReadObservation=-1;
?>
