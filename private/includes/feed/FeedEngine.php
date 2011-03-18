<?php

class FeedEngine
{

	private $name;
	private $email;
	private $pubSubEnabled;
	private $pubSubscribeAddress;

	public function __construct($name, $email, $pub, $pubSubscribeAddress)
	{
		$this->name = $name;
		$this->email = $email;
		$this->pubSubEnabled = $pub;
		$this->pubSubscribeAddress = $pubSubscribeAddress;
	}
	
	public function getFeedAuthor()
	{
		return $this->name;
	}
	
	public function getFeedEmail()
	{
		return $this->email;
	}
	
	public function pubSubHubBubEnabled()
	{
		return $this->pubSubEnabled;
	}
	
	public function pubSubHubBubSubscribeUrl()
	{
		return $this->pubSubHubBubSubscribe;
	}
}
?>