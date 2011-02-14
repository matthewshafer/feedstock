<?php
require_once('../../teency/Teency/Teency.php');


class AllTests extends TestSuite
{
	public function tests()
	{
		require_once('RouterTest.php');
		$this->load('RouterTest');
	}
}

$run = new AllTests();
$run->tests();
?>