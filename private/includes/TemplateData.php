<?php

class TemplateData
{
	private $data = array();
	private $dataCt = -1;
	
	public function __construct()
	{
	
	}
	
	public function addData($data)
	{
		$this->data[] = $data;
		++$this->dataCt;
	}
	
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