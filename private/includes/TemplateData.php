<?php

class TemplateData
{
	private $data = array();
	
	public function __construct()
	{
	
	}
	
	public function addData($data)
	{
		$this->data[] = $data;
	}
}
?>