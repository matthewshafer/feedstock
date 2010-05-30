<?php require_once("header.php"); ?>
			<?php
			foreach($this->templateEngine->getTagData() as $key)
			{
				//echo 'Tag: <a href="''" . $key['Name'] . ' ' . 'URI: ' . $key['URIName'] . '<br><br>';
				
				echo 'Tag: <a href="' . sprintf("%s%s%s", $this->templateEngine->getAdminURL(), "/index.php/tags/", $key["URIName"]) . '">' . $key["Name"] . '</a> ' . 'URI: ' . $key["URIName"] . '<br><br>';
			}
			?>
<?php require_once("footer.php"); ?>