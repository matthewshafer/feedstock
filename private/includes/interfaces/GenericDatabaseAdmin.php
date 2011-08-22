<?php

// extends GenericDatabase because there are still some things we need from the base database
interface GenericDatabaseAdmin extends GenericDatabase
{

	//public function __construct($username, $password, $serverAddress, $databaseName, $tablePrefix, $cacher = null, $lazy = true);
	
	public function addPost($title, $data, $niceTitle, $uri, $author, $date, $draft, $postId = null);
	
	public function deletePost($postId);
	
	public function addPage($title, $data, $niceTitle, $uri, $author, $date, $draft, $corral = null, $pageId = null);
	
	public function removePage($pageId);
	
	public function addUser($username, $displayName, $passwordHash, $salt, $permissions = 99, $canAdministrateUsers = 0);
	
	public function removeUser($userIdToRemove, $currentUserID);
	
	public function getUserByUserName($username);
	
	public function getPostIdNiceCheckedTitle($niceTitle);
	
	public function checkDuplicateUri($type, $uri, $postOrPageId = null);
	
	public function checkDuplicateTitle($type, $niceTitle, $postPageSnippetIt = null);
	
	public function updateCookieVal($userId, $cookieValue = "");
	
	public function findCookie($cookieValue);
	
	public function getPostDataById($postId);
	
	public function getPageDataById($pageId);
	
	//public function getPostList($limit, $offset);
	
	public function getPageList($limit, $offset);
	
	public function addCategory($name, $niceTitle);
	
	public function getSinglePostCategories($postId);
	
	public function getSinglePostTags($id);
	
	public function processPostCategories($id, $categoryArray);
	
	public function unlinkPostCategoriessAndTags($id);
	
	public function processTags($id, $tagArray);
	
	public function getCorralList();
	
	public function getPagesInCorral($name);
	
	public function addSnippet($name, $data, $id = null);
	
	public function removeSnippet($id);
	
	public function getSnippetList();
	
	public function getSnippetById($id);
	
	public function getAllPostsSitemap();
	
	public function getAllPagesSitemap();

}

?>