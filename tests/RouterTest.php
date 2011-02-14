<?php
require_once('../private/includes/Router.php');

class RouterTest extends UnitTest
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

	public function testBaseUriSlashRequestSlashHtaccess()
	{
		$refObj = new ReflectClass('Router', array(true, "/"));
		
		$router = $refObj->getReflection();
		
		$router->buildRouting();
		
		assert($router->fullURI() === "");
		
		assert($router->pageType() === "");
		
		assert($router->fullURIRemoveTrailingSlash() === "");
		
		assert($router->getUriPosition(1) === null);
		
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 0);
		
		assert($router->searchURI("/") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 0);
	}
	
	public function testBaseUriSlashRequestSlashNoHtaccess()
	{
		$refObj = new ReflectClass('Router', array(false, "/"));
		
		$router = $refObj->getReflection();
		
		$router->buildRouting();
		
		assert($router->fullURI() === "");
		
		assert($router->pageType() === "");
		
		assert($router->fullURIRemoveTrailingSlash() === "");
		
		assert($router->getUriPosition(1) === null);
		
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 0);
		
		assert($router->searchURI("/") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 0);
	}
	
	public function testBaseUriSlashRequestPageHtaccess()
	{
		$refObj = new ReflectClass('Router', array(true, "/"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/page/1";
		
		$router->buildRouting();
		
		assert($router->fullURI() === "page/1");
		
		assert($router->pageType() === "page");
		
		assert($router->fullURIRemoveTrailingSlash() === "page/1");
		
		assert($router->getUriPosition(1) === "page");
		
		assert($router->getUriPosition(2) === "1");
		
		assert($router->getUriPosition(3) === null);
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 2);
		
		assert($router->searchURI("page") === 1);
		assert($router->searchURI("1") === 2);
		assert($router->searchURI("post") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 0);
	}
	
	
	public function testBaseUriSlashRequestPageNoHtaccess()
	{
		$refObj = new ReflectClass('Router', array(false, "/"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/index.php/page/2";
		
		$router->buildRouting();
		
		assert($router->fullURI() === "page/2");
		
		assert($router->pageType() === "page");
		
		assert($router->fullURIRemoveTrailingSlash() === "page/2");
		
		assert($router->getUriPosition(1) === "page");
		
		assert($router->getUriPosition(2) === "2");
		
		assert($router->getUriPosition(3) === null);
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 2);
		
		assert($router->searchURI("page") === 1);
		assert($router->searchURI("2") === 2);
		assert($router->searchURI("post") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 1);
	}
	
	public function testBaseUriSlashRequestPageTrailingHtaccess()
	{
		$refObj = new ReflectClass('Router', array(true, "/"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/page/1/";
		
		$router->buildRouting();
		
		assert($router->fullURI() === "page/1");
		
		assert($router->pageType() === "page");
		
		assert($router->fullURIRemoveTrailingSlash() === "page/1");
		
		assert($router->getUriPosition(1) === "page");
		
		assert($router->getUriPosition(2) === "1");
		
		assert($router->getUriPosition(3) === null);
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 2);
		
		assert($router->searchURI("page") === 1);
		assert($router->searchURI("1") === 2);
		assert($router->searchURI("post") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 0);
	}
	
	
	public function testBaseUriSlashRequestPageTrailingNoHtaccess()
	{
		$refObj = new ReflectClass('Router', array(false, "/"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/index.php/page/2/";
		
		$router->buildRouting();
		
		assert($router->fullURI() === "page/2");
		
		assert($router->pageType() === "page");
		
		assert($router->fullURIRemoveTrailingSlash() === "page/2");
		
		assert($router->getUriPosition(1) === "page");
		
		assert($router->getUriPosition(2) === "2");
		
		assert($router->getUriPosition(3) === null);
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 2);
		
		assert($router->searchURI("page") === 1);
		assert($router->searchURI("2") === 2);
		assert($router->searchURI("post") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 1);
	}

	
	public function testBaseUriSomethingRequestBaseHtaccess()
	{
		$refObj = new ReflectClass('Router', array(true, "/test/feedstock/"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/test/feedstock/page/2";
		
		$router->buildRouting();
		
		assert($router->fullURI() === "page/2");
		
		assert($router->pageType() === "page");
		
		assert($router->fullURIRemoveTrailingSlash() === "page/2");
		
		assert($router->getUriPosition(1) === "page");
		
		assert($router->getUriPosition(2) === "2");
		
		assert($router->getUriPosition(3) === null);
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 2);
		
		assert($router->searchURI("page") === 1);
		assert($router->searchURI("2") === 2);
		assert($router->searchURI("post") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 1);
	}
	
	public function testBaseUriSomethingRequestBaseNoHtaccess()
	{
		$refObj = new ReflectClass('Router', array(false, "/test/feedstock/"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/test/feedstock/index.php/page/2";
		
		$router->buildRouting();
		
		assert($router->fullURI() === "page/2");
		
		assert($router->pageType() === "page");
		
		assert($router->fullURIRemoveTrailingSlash() === "page/2");
		
		assert($router->getUriPosition(1) === "page");
		
		assert($router->getUriPosition(2) === "2");
		
		assert($router->getUriPosition(3) === null);
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 2);
		
		assert($router->searchURI("page") === 1);
		assert($router->searchURI("2") === 2);
		assert($router->searchURI("post") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 1);
	}
	
		public function testBaseUriSomethingRequestBaseTrailingHtaccess()
	{
		$refObj = new ReflectClass('Router', array(true, "/test/feedstock/"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/test/feedstock/page/2/";
		
		$router->buildRouting();
		
		assert($router->fullURI() === "page/2");
		
		assert($router->pageType() === "page");
		
		assert($router->fullURIRemoveTrailingSlash() === "page/2");
		
		assert($router->getUriPosition(1) === "page");
		
		assert($router->getUriPosition(2) === "2");
		
		assert($router->getUriPosition(3) === null);
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 2);
		
		assert($router->searchURI("page") === 1);
		assert($router->searchURI("2") === 2);
		assert($router->searchURI("post") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 1);
	}
	
	public function testBaseUriSomethingRequestBaseTrailingNoHtaccess()
	{
		$refObj = new ReflectClass('Router', array(false, "/test/feedstock/"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/test/feedstock/index.php/page/2/";
		
		$router->buildRouting();
		
		assert($router->fullURI() === "page/2");
		
		assert($router->pageType() === "page");
		
		assert($router->fullURIRemoveTrailingSlash() === "page/2");
		
		assert($router->getUriPosition(1) === "page");
		
		assert($router->getUriPosition(2) === "2");
		
		assert($router->getUriPosition(3) === null);
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 2);
		
		assert($router->searchURI("page") === 1);
		assert($router->searchURI("2") === 2);
		assert($router->searchURI("post") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 1);
	}
	
	public function testBaseUriSomethingNoTrailingSlashRequestBaseHtaccess()
	{
		$refObj = new ReflectClass('Router', array(true, "/test/feedstock"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/test/feedstock/page/2";
		
		$router->buildRouting();
		
		assert($router->fullURI() === "page/2");
		
		assert($router->pageType() === "page");
		
		assert($router->fullURIRemoveTrailingSlash() === "page/2");
		
		assert($router->getUriPosition(1) === "page");
		
		assert($router->getUriPosition(2) === "2");
		
		assert($router->getUriPosition(3) === null);
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 2);
		
		assert($router->searchURI("page") === 1);
		assert($router->searchURI("2") === 2);
		assert($router->searchURI("post") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 1);
	}
	
	public function testBaseUriSomethingNoTrailingSlashRequestBaseNoHtaccess()
	{
		$refObj = new ReflectClass('Router', array(false, "/test/feedstock"));
		
		$router = $refObj->getReflection();
		
		$router->uri = "/test/feedstock/index.php/page/2";
		
		$router->buildRouting();
		
		assert($router->fullURI() === "page/2");
		
		assert($router->pageType() === "page");
		
		assert($router->fullURIRemoveTrailingSlash() === "page/2");
		
		assert($router->getUriPosition(1) === "page");
		
		assert($router->getUriPosition(2) === "2");
		
		assert($router->getUriPosition(3) === null);
		assert($router->getUriPosition(0) === null);
		
		assert($router->requestMethod() === "GET");
		
		assert($router->uriLength() === 2);
		
		assert($router->searchURI("page") === 1);
		assert($router->searchURI("2") === 2);
		assert($router->searchURI("post") === -1);
		// doesnt search for partial matches
		assert($router->searchURI("ge") === -1);
		
		// might want to test for even uri parts but i don't think we use that
		
		
		// the page offset is 1 less than the current page.  Page 1 should have an offset of 0 since it is the first page and there is no offset
		assert($router->getPageOffset() === 1);
	}

}
?>