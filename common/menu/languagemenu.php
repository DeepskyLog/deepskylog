<?php // language.php - menu which allows a non-registered user to change the language he sees the information in 
if($languageMenu==1)
{ echo "<div class=\"menuDiv\">";
  echo "<p class=\"menuHead\">".LangLanguageMenuTitle."</p>";
	echo "<form action=\"".$baseURL."index.php\" method=\"post\">";
	echo "<div>";
	echo "<input type=\"hidden\" name=\"indexAction\" value=\"setLanguage\" />";
	echo "<select name=\"language\" class=\"menuField menuDropdown\" onchange=\"this.form.submit()\">";
  $previous_language=$_SESSION['lang'];
  $languages = $objLanguage->getLanguages();
  while(list ($key, $value) = each($languages))
    echo "<option value=\"".$key."\"".(($key==$_SESSION['lang'])?" selected=\"selected\"":'').">".$value."</option>";
	echo "</select>";
	echo "</div>";
  echo "</form>";
	echo "</div>";
}
?>