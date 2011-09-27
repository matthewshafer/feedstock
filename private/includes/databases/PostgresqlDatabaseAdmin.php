<?php
require_once("PostgresqlDatabase.php");

class PostgresqlDatabaseAdmin extends PostgresDatabase implements GenericDatabaseAdmin
{

	/**
	 * __construct function.
	 * 
	 * @brief Creates the database connection
	 * @access public
	 * @param mixed $username
	 * @param mixed $password
	 * @param mixed $serverAddress
	 * @param mixed $databaseName
	 * @param mixed $tablePrefix
	 * @return void
	 */
	public function __construct($username, $password, $serverAddress, $serverPort, $databaseName, $tablePrefix, $cacher = null, $lazy = true)
	{
		parent::__construct($username, $password, $serverAddress, $serverPort, $databaseName, $tablePrefix, null, false);
	}

	public function addPost($title, $data, $niceTitle, $uri, $author, $date, $draft, $postId = null)
	{
	
	}
	
	public function deletePost($postId)
	{
	
	}
	
	public function addPage($title, $data, $niceTitle, $uri, $author, $date, $draft, $corral = null, $pageId = null)
	{
	
	}
	
	public function removePage($pageId)
	{
	
	}
	
	public function addUser($username, $displayName, $passwordHash, $salt, $permissions = 99, $canAdministrateUsers = 0)
	{
	
	}
	
	public function removeUser($userIdToRemove, $currentUserID)
	{
	
	}
	
	public function getUserByUserName($username)
	{
	
	}
	
	public function getPostIdNiceCheckedTitle($niceTitle)
	{
	
	}
	
	public function checkDuplicateUri($type, $uri, $postOrPageId = null)
	{
	
	}
	
	public function checkDuplicateTitle($type, $niceTitle, $postPageSnippetId = null)
	{
	
	}
	
	public function updateCookieVal($userId, $cookieValue = "")
	{
	
	}
	
	public function findCookie($cookieValue)
	{
	
	}
	
	public function getPostDataById($postId)
	{
	
	}
	
	public function getPageDataById($pageId)
	{
	
	}
	
	//public function getPostList($limit, $offset);
	
	public function getPageList($limit, $offset)
	{
	
	}
	
	public function addCategory($name, $niceTitle)
	{
	
	}
	
	public function getSinglePostCategories($postId)
	{
	
	}
	
	public function getSinglePostTags($postId)
	{
	
	}
	
	public function processPostCategories($postId, $categoryArray)
	{
	
	}
	
	public function unlinkPostCategoriessAndTags($postId)
	{
	
	}
	
	public function processTags($postId, $tagArray)
	{
	
	}
	
	public function getCorralList()
	{
	
	}
	
	public function getPagesInCorral($name)
	{
	
	}
	
	public function addSnippet($name, $data, $snippetId = null)
	{
	
	}
	
	public function removeSnippet($snippetId)
	{
	
	}
	
	public function getSnippetList()
	{
	
	}
	
	public function getSnippetById($snippetId)
	{
	
	}
	
	public function getAllPostsSitemap()
	{
	
	}
	
	public function getAllPagesSitemap()
	{
	
	}
}

?>