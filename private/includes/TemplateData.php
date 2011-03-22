<?php


/**
 * TemplateData class.
 *
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * Hold's data for the templates
 *
 */
class TemplateData
{
	private $data = array();
	private $dataCt = -1;
	
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
	
	}
	
	
	/**
	 * addData function.
	 * 
	 * @access public
	 * @param mixed $data
	 * @return void
	 */
	public function addData($data)
	{
		$this->data[] = $data;
		++$this->dataCt;
	}
	
	
	/**
	 * getData function.
	 * 
	 * @access public
	 * @param int $pos (default: 0)
	 * @return mixed
	 */
	public function getData($pos = 0)
	{
		// if the position is larger than the total number of items in the array an exception is thrown
		if($pos > $this->dataCt)
		{
			throw new exception("Invalid Data Position");
		}
		
		return $this->data[$pos];
	}
}
?>