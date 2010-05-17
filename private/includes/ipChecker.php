<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief checks Ip address, it's super basic at the moment
 * 
 */
 
 class ipChecker
 {
 	private $ipAddress = null;
 	
 	
 	public function __construct()
 	{
 		
 	}
 	
 	private function generateIP()
 	{
 		if(!empty($_SERVER["HTTP_CLIENT_IP"]))
 		{
 			$tmp = $_SERVER["HTTP_CLIENT_IP"];
 			$tmp2 = explode(".", $tmp);
 			
 			if($this->validIP($tmp2))
 			{
 				$this->ipAddress = $tmp;
 			}
 		}
 		
 		if($this->ipAddress == null && !empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
 		{
 			$tmp = $_SERVER["HTTP_X_FORWARDED_FOR"];
 			$tmp2 = explode(".", $tmp);
 			
 			if($this->validIP($tmp2))
 			{
 				$this->ipAddress = $tmp;
 			}
 		}
 		
 		if($this->ipAddress == null && !empty($_SERVER["REMOTE_ADDR"]))
 		{
 			$this->ipAddress = $_SERVER["REMOTE_ADDR"];
 		}
 	}
 	
 	public function checkIP($ip)
 	{
 		$this->generateIP();
 		$return = false;
 		
 		if($ip == $this->ipAddress)
 		{
 			$return = true;
 		}
 		
 		return $return;
 	}
 	
 	// need a lot better checking in here
 	private function validIP($ipArr)
 	{
 		$return = false;
 		$ct = count($ipArr);
 		
 		if($ct == 4)
 		{
 			if($ipArr[1] != "127")
 			{
 				$return = true;
 			}
 		}
 		
 		return $return;
 	}
 
 }
?>