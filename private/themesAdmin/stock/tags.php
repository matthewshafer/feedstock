<?php require_once("header.php"); ?>
			<?php
			foreach($this->templateEngine->getTagData() as $key)
			{
				echo "Tag: " . $key["Name"] . " " . "URI: " . $key["URIName"] . "<br><br>";
			}

			echo "<br><br> The array of all the data<br>";
			print_r($this->templateEngine->getTagData());
			?>
<?php require_once("footer.php"); ?>