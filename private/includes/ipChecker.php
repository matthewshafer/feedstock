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
 	private $customHeader;
 	
 	
 	public function __construct($customHeader = null)
 	{
 		$this->customHeader = $customHeader;
 	}
 	
 	private function generateIP()
 	{
 	 	// REMOTE_ADDR header should always be set when we are looking for the ip address.  Apache sets it by default.
 	 	// the customHeader was added in order to specify your own header to grab the IP from
 		if($this->validIP($_SERVER["REMOTE_ADDR"]))
 		{
 			$this->ipAddress = $_SERVER["REMOTE_ADDR"];
 		}
 		else if(!empty($_SERVER["X_REMOTE_IP"]) && $this->validIP($_SERVER["X_REMOTE_IP"]))
 		{
 			$this->ipAddress = $_SERVER["X_REMOTE_IP"];
 		}
 		else if(!empty($_SERVER["X_FORWARDED_FOR"]) && $this->validIP($_SERVER["X_FORWARDED_FOR"]))
 		{
 			$this->ipAddress = $_SERVER["X_FORWARDED_FOR"];
 		}
 		else
 		{
 			$this->ipAddress = null;
 		}
 	}
 	
 	private function getIPFromCustomHeader()
 	{
 		if(!empty($_SERVER[$this->customHeader]))
 		{
 			$this->ipAddress = $_SERVER[$this->customHeader];
 		}
 		else
 		{
 			$this->ipAddress = $_SERVER["REMOTE_ADDR"];
 		}
 	}
 	
 	public function checkIP($ip)
 	{
 		if($this->customHeader == null)
 		{
 			$this->generateIP();
 		}
 		else
 		{
 			$this->getIPFromCustomHeader();
 		}
 		$return = false;
 		
 		if($ip == $this->ipAddress)
 		{
 			$return = true;
 		}
 		
 		return $return;
 	}
 	
 	// need a lot better checking in here
 	private function validIP()
 	{
 		$ipArr = explode(".", $this->ipAddress);
 		$return = false;
 		$keepSearching = true;
 		$ct = count($ipArr);
 		
 		if($ct == 4)
 		{
 			// these check the private ranges to see if we are getting a private ip
 			// I could probably make this a lot faster but I need to come up with a way.
 			if(!$this->classSearch($this->ipAddress, "0.0.0.0", "255.255.255.255"))
 			{
 				$keepSearching = false;
 			}
 			
 			if($keepSearching && $this->classSearch($this->ipAddress, "0.0.0.0", "0.255.255.255"))
 			{
 				$keepSearching = false;
 			}
 			
 			if($keepSearching && $this->classSearch($this->ipAddress, "10.0.0.0", "10.255.255.255"))
 			{
 				$keepSearching = false;
 			}
 			
 			if($keepSearching && $this->classSearch($this->ipAddress, "127.0.0.0", "127.255.255.255"))
 			{
 				$keepSearching = false;
 			}
 			
 			if($keepSearching && $this->classSearch($this->ipAddress, "128.0.0.0", "128.0.255.255"))
 			{
 				$keepSearching = false;
 			}
 			
 			if($keepSearching && $this->classSearch($this->ipAddress, "169.254.0.0", "169.254.255.255"))
 			{
 				$keepSearching = false;
 			}
 			
 			if($keepSearching && $this->classSearch($this->ipAddress, "172.16.0.0", "172.31.255.255"))
 			{
 				$keepSearching = false;
 			}
 			
 			if($keepSearching && $this->classSearch($this->ipAddress, "191.255.0.0", "191.255.255.255"))
 			{
 				$keepSearching = false;
 			}
 			
 			if($keepSearching && $this->classSearch($this->ipAddress, "192.0.0.0", "192.0.0.255"))
 			{
 				$keepSearching = false;
 			}
 			
 			if($keepSearching && $this->classSearch($this->ipAddress, "192.168.0.0", "192.168.255.255"))
 			{
 				$keepSearching = false;
 			}
 			
 			if($keepSearching && $this->classSearch($this->ipAddress, "223.255.255.0", "223.255.255.255"))
 			{
 				$keepSearching = false;
 			}
 			
 			if($keepSearching)
 			{
 				$return = true;
 			}
 		}
 		
 		return $return;
 	}
 	
 	
 	private function classSearch($ipAddr, $startAddr, $finishAddr)
 	{
 		$startArr = explode(".", $startAddr);
 		$finishArr = explode(".", $finishAddr);
 		$ipArr = explode(".", $ipAddr);
 		$return = false;
 		
 		// I would totally do this recursively with another function but I need to find out how php handles recursion and if it uses a bunch more memory when things are recursive
 		if($ipArr[0] >= $startArr[0] && $ipArr[0] <= $finishArr[0])
 		{
 			if($ipArr[1] >= $startArr[1] && $ipArr[1] <= $finishArr[1])
 			{
 				if($ipArr[2] >= $startArr[2] && $ipArr[2] <= $finishArr[2])
 				{
 					if($ipArr[3] >= $startArr[3] && $ipArr[4] <= $finishArr[3])
 					{
 						$return = true;
 					}
 				}
 			}
 		}
 		
 		return $return;
 	}
 
 }
?>