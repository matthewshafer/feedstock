<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief checks Ip address.  Doesn't handle hostnames, so to it localhost != 127.0.0.1
 * 
 */
 
 class ipChecker
 {
 	private $ipAddress = null;
 	private $customHeader;
 	
 	/**
 	 * __construct function.
 	 * 
 	 * @access public
 	 * @param mixed $customHeader. (default: null)
 	 * @return void
 	 */
 	public function __construct($customHeader = null)
 	{
 		$this->customHeader = $customHeader;
 	}
 	
 	/**
 	 * generateIP function.
 	 * 
 	 * @brief figures out a valid IP address to use based on a few headers that give us IP addresses.
 	 * @access private
 	 * @return void
 	 */
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
 	
 	/**
 	 * getIPFromCustomHeader function.
 	 * 
 	 * @brief allows the usage of a custom header that contains the IP address.  Useful if using a proxy solution on the front end.
 	 * @access private
 	 * @return void
 	 */
 	private function getIPFromCustomHeader()
 	{
 		if(!empty($_SERVER[$this->customHeader]))
 		{
 			$this->ipAddress = $_SERVER[$this->customHeader];
 		}
 		else
 		{
 			$this->ipAddress = null;
 		}
 	}
 	
 	/**
 	 * checkIP function.
 	 * 
 	 * @brief Checks the IP from the headers with one we are specifying.
 	 * @access public
 	 * @param mixed $ip
 	 * @return Boolean. True if the IP is a match, false if not a match
 	 */
 	public function checkIP($ip)
 	{
 		$return = false;
 		
 		if($this->customHeader == null)
 		{
 			$this->generateIP();
 		}
 		else
 		{
 			$this->getIPFromCustomHeader();
 		}
 		
 		if($ip == $this->ipAddress)
 		{
 			$return = true;
 		}
 		
 		return $return;
 	}
 	
 	/**
 	 * validIP function.
 	 * 
 	 * @brief Checks to see if an IP address falls into the parameters of being valid, so 0.0.0.0 to 255.255.255.255 and is not a private address.
 	 * @access private
 	 * @return Boolean. True if valid, false if not valid
 	 */
 	private function validIP()
 	{
 		$ipArr = explode(".", $this->ipAddress);
 		$return = false;
 		$keepSearching = true;
 		$ct = count($ipArr);
 		$ipFilterArr = array(
 			array("0.0.0.0", "0.255.255.255"),
 			array("10.0.0.0", "10.255.255.255"),
 			array("127.0.0.0", "127.255.255.255"),
 			array("128.0.0.0", "128.0.255.255"),
 			array("169.254.0.0", "169.254.255.255"),
 			array("172.16.0.0", "172.31.255.255"),
 			array("191.255.0.0", "191.255.255.255"),
 			array("192.0.0.0", "192.0.0.255"),
 			array("192.168.0.0", "192.168.255.255"),
 			array("223.255.255.0", "223.255.255.255")
 		);
 		
 		if($ct == 4)
 		{
 			$filterCt = count($ipFilterArr);
 			$i = 0;
 			
 			if($this->classSearch($this->ipAddress, "0.0.0.0", "255.255.255.255"))
 			{
 				while($keepSearching && $i < $filterCt)
 				{
 					if($this->classSearch($this->ipAddress, $ipFilterArr[$i][0], $ipFilterArr[$i][1]))
 					{
 						$keepSearching = false;
 					}
 				
 					$i++;
 				}
 			
 				if($keepSearching)
 				{
 					$return = true;
 				}
 			}
 		}
 		
 		return $return;
 	}
 	
 	/**
 	 * classSearch function.
 	 * 
 	 * @brief Does the cecking to see if an IP address falls inside the given start and finish address.
 	 * @access private
 	 * @param mixed $ipAddr
 	 * @param mixed $startAddr
 	 * @param mixed $finishAddr
 	 * @return Boolean. True if a match, false if not a match
 	 */
 	private function classSearch($ipAddr, $startAddr, $finishAddr)
 	{
 		$startArr = explode(".", $startAddr);
 		$finishArr = explode(".", $finishAddr);
 		$ipArr = explode(".", $ipAddr);
 		$return = false;
 		
 		// I would totally do this recursively with another function but I need to find out how php handles recursion and if it uses more memory when things are recursive
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