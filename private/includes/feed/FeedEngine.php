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
		$this->pubsubEnabled = $pub;
		$this->pubSubscribeAddress = $pubSubscribeAddress;
	}
	
	public function getFeedAuthor()
	{
		return $this->feedAuthor;
	}
	
	public function getFeedEmail()
	{
		return $this->feedAuthorEmail;
	}
	
	public function pubSubHubBubEnabled()
	{
		return $this->feedPubSubHubBub;
	}
	
	public function pubSubHubBubSubscribeUrl()
	{
		return $this->feedPubSubHubBubSubscribe;
	}
}
?>