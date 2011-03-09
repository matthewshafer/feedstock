<?php
require_once('../../teency/Teency/Teency.php');


class AllTests extends TestSuite
{
	public function tests()
	{
		require_once('RouterTest.php');
		$this->load('RouterTest');
		
		require_once('IpCheckerTest.php');
		$this->load('IpCheckerTest');
	}
}

$run = new AllTests();
$run->tests();
?>