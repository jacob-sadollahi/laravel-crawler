<?php
namespace Hamrahnegar\Crawler\HtmlParser;

use Goutte\Client;

use GuzzleHttp\Client as GuzzleClient;

use GuzzleHttp\Psr7\Request;


class RequestContent {

	// url 
	private $url;

	// content
	private $content;

	public function __construct ($url){

		$this->url = $url;

		$this->_client();
	}

	public static function request ($url)
	{
		return new self($url);
	}

	public function getUrl ()
	{
		return $this->url;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function getContent ()
	{
		return $this->content;
	}

	public function setContent ($content)
	{
		$this->content = $content;
	} 

	private function _client()
	{
		$goutteClient = new Client();
		$guzzleClient = new GuzzleClient(array(
		    'timeout' => config('crawler.timeout'),
		    'verify' => config('crawler.verify'),
		));
		
		$goutteClient->setClient($guzzleClient);
		$this->content = $goutteClient->request('GET', $this->url);
		
	}

		
}

