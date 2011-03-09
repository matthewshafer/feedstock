<?php
require_once('../private/includes/IpChecker.php');

class IpCheckerTest extends UnitTest
{
	
	public function setUpTest()
	{
		$_SERVER["REMOTE_ADDR"] = "";
		$_SERVER["X_REMOTE_IP"] = "";
		$_SERVER["X_FORWARDED_FOR"] = "";
	}
	
	public function tearDownTest()
	{
		unset($_SERVER["REMOTE_ADDR"]);
		unset($_SERVER["X_REMOTE_IP"]);
		unset($_SERVER["X_FORWARDED_FOR"]);
	}
	
	public function testInvalidIp()
	{
		$refObj = new ReflectClass('IpChecker', null);
		
		$ipChecker = $refObj->getReflection();
		
		assert($ipChecker->validIP("-1.0.0.0") === false);
		
		assert($ipChecker->validIP("0.0.0.-1") === false);
		
		assert($ipChecker->validIP("0.0.0.0") === false);
		
		assert($ipChecker->validIP("0.0.0.256") === false);
		
		assert($ipChecker->validIP("256.0.0.0") === false);
		
		assert($ipChecker->validIP("127.0.0.1") === false);
		
		assert($ipChecker->validIP("150.0.0.-1") === false);
		
		assert($ipChecker->validIP("0.0.0.256") === false);
	}
	
	public function testValidIp()
	{
		$refObj = new ReflectClass('IpChecker', null);
		
		$ipChecker = $refObj->getReflection();
		
		assert($ipChecker->validIP("1.0.0.0") === true);
		
		assert($ipChecker->validIP("123.45.67.89") === true);
		
		assert($ipChecker->validIP("55.55.0.2") === true);
	}
}
?>