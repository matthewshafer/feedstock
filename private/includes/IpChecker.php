<?php
/**
 * @file
 * @author Matthew Shafer <matt@niftystopwatch.com>
 * @brief checks Ip address.  Doesn't handle hostnames, so to it localhost != 127.0.0.1.  Only does a few basic checks to see if the Ip is valid.
 * 
 */
 
 class IpChecker
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
 	public function checkIP($ipToCheck)
 	{
 		$return = false;
 		
 		if($this->customHeader === null)
 		{
 			$this->generateIP();
 		}
 		else
 		{
 			$this->getIPFromCustomHeader();
 		}
 		
 		if($ipToCheck === $this->ipAddress)
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
 	private function validIP($ipAddress)
 	{
 		$ipArray = explode(".", $ipAddress);
 		$return = false;
 		$keepSearching = true;
 		$fallsInParam = true;
 		$ipArrayCount = count($ipArray);
 		$ipFilterArray = array(
 			// 0.0.0.0 - 0.255.255.255
 			array(0, 16777215),
 			// 10.0.0.0 - 10.255.255.255
 			array(167772160, 184549375),
 			// 127.0.0.0 - 127.255.255.255
 			array(2130706432, 2147483647),
 			// 128.0.0.0 - 128.0.255.255
 			array(2147483648, 2147549183),
 			// 169.254.0.0 - 169.254.255.255
 			array(2851995648, 2852061183),
 			// 172.16.0.0 - 172.31.255.255
 			array(2886729728, 2887778303),
 			// 191.255.0.0 - 191.255.255.255
 			array(3221159936, 3221225471),
 			// 192.0.0.0 - 192.0.0.255
 			array(3221225472, 3221225727),
 			// 192.168.0.0 - 192.168.255.255
 			array(3232235520, 3232301055),
 			// 223.255.255.0 - 223.255.255.255
 			array(3758096128, 3758096383)
 		);
 		
 		if($ipArrayCount === 4)
 		{
 			// checks to see if each block of the ip falls between 0 and 255
 			for($i = 0; $i < 4; $i++)
 			{
 				if($fallsInParam && (int)$ipArray[$i] >= 0 && (int)$ipArray[$i] <= 255)
 				{
 					// do nothing
 				}
 				else
 				{
 					$fallsInParam = false;
 				}
 			}
 		
 		
 			if($fallsInParam)
 			{
 				$ipFilterCount = count($ipFilterArray);
 				$ipIntVal = ((int)$ipArray[0] * 16777216) + ((int)$ipArray[1] * 65536) + ((int)$ipArray[2] * 256) + ((int)$ipArray[3]);
 				$currentPosition = 0;
 				
 				if($this->classSearch($ipIntVal, 0, 4294967295))
 				{
 					while($keepSearching && $currentPosition < $ipFilterCount)
 					{
 						if($this->classSearch($ipIntVal, $ipFilterArray[$currentPosition][0], $ipFilterArray[$currentPosition][1]))
 						{
 							$keepSearching = false;
 						}
 				
 						++$currentPosition;
 					}
 				
 					if($keepSearching)
 					{
 						$return = true;
 					}
 				}
 			}
 		}
 		
 		return $return;
 	}
 	
 	/**
 	 * classSearch function.
 	 * 
 	 * @brief Does the checking to see if an IP address falls inside the given start and finish address.
 	 * @access private
 	 * @param mixed $ipAddr
 	 * @param mixed $startAddr
 	 * @param mixed $finishAddr
 	 * @return Boolean. True if a match, false if not a match
 	 */
 	private function classSearch($ipAddr, $startAddr, $finishAddr)
 	{
 		$return = false;
 		
 		if($ipAddr >= $startAddr && $ipAddr <= $finishAddr)
 		{
 			$return = true;
 		}
 		
 		return $return;
 	}
 
 }
?>