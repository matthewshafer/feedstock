<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief Does all the heavy lifting for admin page themes
 * 
 */
class templateEngineAdmin
{
	protected $db;
	protected $router;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $database
	 * @param mixed $router
	 * @return void
	 */
	public function __construct($database, $router)
	{
		$this->db = $database;
		$this->router = $router;
	}
	
	/**
	 * getThemeLoc function.
	 * 
	 * @access public
	 * @return void
	 */
	public function getThemeLoc()
	{
		return $this->request();
	}
	
	/**
	 * request function.
	 * 
	 * @access private
	 * @return void
	 */
	private function request()
	{
		
	}
}
?>