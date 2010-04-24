<?php
require_once("header.php");

//echo "<br><br>this is the theme talking<br>";

//print_r($this->templateEngine->getPageData());

	echo '
	<a href="' . $this->templateEngine->getPageURL() . '">' . $this->templateEngine->getPageTitle() . '</a>';
	echo "<br>";
	echo "Body: " . $this->templateEngine->getPageBodyHTML();
	echo "<br>";
	echo "Date: " . $this->templateEngine->getPageTime("m/d/y");
	echo "<br>";
	echo "<br>";

require_once("footer.php");

?>