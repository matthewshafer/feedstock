<?php

/**
 * GenericDatabase interface.
 * 
 * Interface required to be implemented by all od the different database addons
 * @author Matthew Shafer <matt@niftystopwatch.com>
*/
interface GenericDatabase
{
	public function __construct($username, $password, $serverAddress, $serverPort, $databaseName, $tablePrefix, $cacher = null, $lazy = true);
	
	public function closeConnection();
	
	public function getPosts($limit, $offset, $draft = false);
	
	public function getPage($uri, $draft = false);
	
	public function getSinglePost($uri, $draft = false);
	
	public function getPostCategoryOrTag($idArray, $type);
	
	public function getPostsInCategoryOrTag($type, $limit, $offset, $draft = false);
	
	//public function getCategoryOrTag($ID, $type);
	
	public function checkCategoryOrTagName($name, $type);
	
	public function listCategoriesOrTags($type);
	
	public function haveNextPage();
	
	public function getCorralByName($name, $draft = 0);
	
	public function getSnippetByName($name);
	
	public function enableDebug();
}

?>