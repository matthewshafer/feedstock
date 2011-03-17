<?php
require_once('../private/includes/TemplateRouter.php');
require_once('../private/includes/Router.php');

class TemplateRouterTest extends UnitTest
{

	public function setUpTest()
	{
		// for the tests we are changing this after the test is created by using a reflection to change the value of the variables
		$_SERVER['REQUEST_URI'] = "/";
		$_SERVER['REQUEST_METHOD'] = "GET";
	}
	
	public function tearDownTest()
	{
		unset($_SERVER['REQUEST_URI']);
		unset($_SERVER['REQUEST_METHOD']);
	}
	
	public function testCategoryPass()
	{
		$refObj = new ReflectClass('Router', array(true, "/"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/category/test";
		
		$router->buildRouting();
		
		$database = new FakeObject();
		
		$database->addMethod('getPostsInCategoryOrTag', 'thisWorks');
	}

}
?>