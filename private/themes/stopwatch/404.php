<?php

echo "Oops! Looks like we lost something. blarg <br>";
//echo $this->templateEngine->getError();
echo "<pre>";
var_dump(debug_backtrace());
echo "</pre>";


?>