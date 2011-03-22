<?php

/**
 * FeedEngine class.
 *
 * @author Matthew Shafer <matt@niftystopwatch.com>
 *
 * Holds things needed by feeds like author/email and some pubsubhubbub stuff
 * 
 */
class FeedEngine
{

	private $name;
	private $email;
	private $pubSubEnabled;
	private $pubSubscribeAddress;

	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $name
	 * @param mixed $email
	 * @param mixed $pub
	 * @param mixed $pubSubscribeAddress
	 * @return void
	 */
	public function __construct($name, $email, $pub, $pubSubscribeAddress)
	{
		$this->name = $name;
		$this->email = $email;
		$this->pubSubEnabled = $pub;
		$this->pubSubscribeAddress = $pubSubscribeAddress;
	}
	
	
	/**
	 * getFeedAuthor function.
	 * 
	 * @access public
	 * @return mixed returns whatever the user specified in the config for the feed author
	 */
	public function getFeedAuthor()
	{
		return $this->name;
	}
	
	
	/**
	 * getFeedEmail function.
	 * 
	 * @access public
	 * @return mixed returns whatever the user specified in the config for the feed author email
	 */
	public function getFeedEmail()
	{
		return $this->email;
	}
	
	
	/**
	 * pubSubHubBubEnabled function.
	 * 
	 * @access public
	 * @return boolean true if pubsubhubbub is enabled false if it isn't
	 */
	public function pubSubHubBubEnabled()
	{
		return $this->pubSubEnabled;
	}
	
	
	/**
	 * pubSubHubBubSubscribeUrl function.
	 * 
	 * @access public
	 * @return mixed returns what the user entered for the pubsubhubbub subscribe url
	 */
	public function pubSubHubBubSubscribeUrl()
	{
		return $this->pubSubHubBubSubscribe;
	}
}
?>