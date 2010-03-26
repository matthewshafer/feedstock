<?php 
foreach($this->templateEngine->getTheData() as $key)
{
	echo "Title: " . $key["Title"] . " " . "URI: " . $key["URI"] . " " . "Date: " . date('m-d-y', strtotime($key["Date"]))  . "<br><br>";
}

echo "<br><br> The array of all the data<br>";
print_r($this->templateEngine->getTheData());
?>