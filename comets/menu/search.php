<?php 
// search.php
// menu which allows the user to search the observation database 

global $inIndex,$loggedUser,$objUtil;

if((!isset($inIndex))||(!$inIndex)) include "../../redirect.php";
else menu_search();

function menu_search()
{ global $baseURL,$loggedUser;
	  echo "<li>
	  	       <a href=\"http://". $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"] ."#\">" . LangSearchMenuTitle."<span class=\"arrow\"></span></a>";
	  echo " <ul>";

	if($loggedUser)
	  echo "  <li><a href=\"".$baseURL."index.php?indexAction=comets_result_query_observations&amp;user=".urlencode($loggedUser)."\">".LangSearchMenuItem1."</a></li>";
	echo "  <li><a href=\"".$baseURL."index.php?indexAction=comets_all_observations\" >".LangSearchMenuItem2."</a></li>";
	echo "  <li><a href=\"".$baseURL."index.php?indexAction=comets_query_observations\" >".LangSearchMenuItem3."</a></li>";
  echo "  <li><a href=\"".$baseURL."index.php?indexAction=comets_view_objects\" >".LangSearchMenuItem4."</a></li>";
  echo "  <li><a href=\"".$baseURL."index.php?indexAction=comets_query_objects\" >".LangSearchMenuItem5."</a></li>";
	echo "  <li><a href=\"".$baseURL."index.php?indexAction=comets_rank_observers\" >".LangSearchMenuItem6."</a></li>";
  echo "  <li><a href=\"".$baseURL."index.php?indexAction=comets_rank_objects\" >".LangSearchMenuItem7."</a></li>";
  echo " </ul>";
  echo "</li>";
}
?>
