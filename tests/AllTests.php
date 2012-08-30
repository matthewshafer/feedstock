<?php
require_once( __DIR__ . '/Teency/Teency.php');


class AllTests extends TestSuite
{
	public function tests()
	{
		require_once('RouterTest.php');
		$this->load('RouterTest');
		
		require_once('IpCheckerTest.php');
		$this->load('IpCheckerTest');
		
		require_once('TemplateRouterTest.php');
		$this->load('TemplateRouterTest');
	}
}

$run = new AllTests();
$run->tests();
?>