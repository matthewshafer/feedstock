<?php
require_once('private/includes/TemplateRouter.php');
require_once('private/includes/TemplateData.php');
require_once('private/includes/Router.php');

class TemplateRouterTest extends UnitTest
{
	public $baseLoc;
	public $themeName = 'stopwatch';
	public $postFormat = '%MONTH%/%DAY%/%YEAR%/%TITLE%';
	
	
	public function setUpTest()
	{
		// for the tests we are changing this after the test is created by using a reflection to change the value of the variables
		$_SERVER['REQUEST_URI'] = "/";
		$_SERVER['REQUEST_METHOD'] = "GET";
		$this->baseLoc = '..';
		//print_r($this->baseLoc);
	}
	
	public function tearDownTest()
	{
		unset($_SERVER['REQUEST_URI']);
		unset($_SERVER['REQUEST_METHOD']);
	}
	
	/*
	public function testCategoryPass()
	{
		$refObj = new ReflectClass('Router', array(true, "/"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/category/test";
		
		$router->buildRouting();
		
		$database = new FakeObject();
		
		$database->addMethod('getPostsInCategoryOrTag', 'thisWorks');
		
		$database->addMethod('checkCategoryOrTagName', true);
		
		$templateData = new TemplateData();
		
		$refObj = new ReflectClass('TemplateRouter', array($router, $database, $templateData, $this->baseLoc, $this->themeName, 'category', 10, $this->postFormat));
		
		$templateRouter = $refObj->getReflection();
		
		assert($templateRouter->templateFile() === '../private/themes/stopwatch/postList.php');
		
		// need to make some checks for this array
		print_r($database->getFakeMethodArgumentsArray());
	}
	
	public function testCategoryPagePass()
	{
		$refObj = new ReflectClass('Router', array(true, "/"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/category/test/page/5";
		
		$router->buildRouting();
		
		$database = new FakeObject();
		
		$database->addMethod('getPostsInCategoryOrTag', 'thisWorks');
		
		$database->addMethod('checkCategoryOrTagName', true);
		
		$templateData = new TemplateData();
		
		$refObj = new ReflectClass('TemplateRouter', array($router, $database, $templateData, $this->baseLoc, $this->themeName, 'category', 10, $this->postFormat));
		
		$templateRouter = $refObj->getReflection();
		
		assert($templateRouter->templateFile() === '../private/themes/stopwatch/postList.php');
		
		// need to make some checks for this array
		print_r($database->getFakeMethodArgumentsArray());
	}

	*/

}
?>