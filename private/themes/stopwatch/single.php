<?php

print_r($this->templateEngine->getPageData());

	if($this->templateEngine->haveError())
	{
		echo $this->templateEngine->getErrorText();
	}

?>