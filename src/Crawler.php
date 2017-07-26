<?php
namespace Hamrahnegar\Crawler;

use Hamrahnegar\Crawler\RequestHead;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Hamrahnegar\Crawler\HtmlParser\Interfaces\iCrawler;
use Hamrahnegar\Crawler\HtmlParser\RequestContent;
use Hamrahnegar\Crawler\HtmlParser\Lib\Aparat;

class Crawler implements iCrawler {

	protected $crawler;

	protected $removeContent = [];

	protected $content = [];

	protected $first_text = false;

	protected $last_text = false;

	protected $detectUrlByRequest = false;

	private $data = [];

	private $base_url;

	private $url;

	private $allLinks;


	public function __construct($url)
	{
		/*$request = new RequestHead('https://www.tasnimnews.com/fa/service/1/%D8%B3%DB%8C%D8%A7%D8%B3%DB%8C');
		dd($request->getStatusCode());*/
		$this->url = $url;

		if ($url != ""){
			$this->crawler = RequestContent::request($url)->getContent();			
		}

		$this->__init__();
	}

	public function __init__()
	{
		$this->baseUrl();
		$this->allLinks();
	}

	private function baseUrl(){
		if ($this->url) {
			$url = parse_url($this->url);
			$this->base_url = $url['scheme'] . '://' . $url['host'];
		}
	}

	public function getImages()
	{
		$old_set = $this->first_text;
		$this->first_text = true;
		$images = $this->getHtmlParser('//img/attribute::src | //img/attribute::data-src');

		foreach ($images as $key => $value) {
			if (! starts_with($value, 'http')) {
				if (str_contains($value, '../')){
					$value =str_replace('../', '', $value);
				}
				$images[$key] = $this->base_url . '/' . ltrim($value, '/');
			}
		}

		$this->first_text = $old_set;
		return $images;
	}

	public function getAllLinks($key = null)
	{
		if ($key != null) {
			$arr = [];
			if (is_string($key)) {
				if (array_has($this->allLinks, $key)) {
					$value = array_get($this->allLinks, $key);
					return array_set($arr, $key, $value);
				}
				return [$key => null];
			}

			if (is_array($key)) {
				foreach ($key as $key_ => $val) {
					if (is_string($val)) {
						if (array_has($this->allLinks, $val)) {
							$get = array_get($this->allLinks, $val);
							array_set($arr, $val, $get);
						}
					} elseif (is_callable($val)) {
						if (array_has($this->allLinks, $key_)) {
							$get = array_get($this->allLinks, $key_);
							$callData = call_user_func_array($val, [$get]);
							array_set($arr, $key_, $callData);
						}
					}
				}
				return $arr;
			}
		}
		return $this->allLinks;
	}

	private function allLinks()
	{
		//ini_set('max_execution_time', 0);
		set_time_limit(0);
		$html = $this->getHtml();
		$paterns = [
			'src' => '/src=\"([^\"|\']*)/is',
			'href' => '/href=\"([^\"|\']*)/is',
			'data-src' => '/data-src="([^\"|\']*)/is',
			'data-href' => '/data-href="([^\"|\']*)/is',
			'http' => '/(https?:\/\/[^\"|\']*)/is',
		];
		$result = [];

		foreach ($paterns as $key => $value) {
			if (preg_match_all($value, $html, $matches)){
				$result[$key] = $matches[1];
			}
		}

		$result = array_unique(array_collapse($result));
		$result = collect($result)->map(function ($item, $key) {

						if (str_contains($item, 'aparat.com')) {
							// https://www.aparat.com/video/video/embed/videohash/KtSQl/vt/frame
							// https://www.aparat.com/embed/KtSQl?data%5Brnddiv%5D=1499693440550823&amp;data%5Bresponsive%5D=yes
							foreach (['/embed\/videohash\/([^\/]*)/is', '/embed\/([^\?]*)/is'] as $patern) {
								if (preg_match_all($patern, $item, $embed)) {
									return 'http://www.aparat.com/v/' . $embed[1][0] . '/';
								}
							}
						}

						if (! starts_with($item, 'http')) {
							if (str_contains($item, '../')){
								$item =str_replace('../', '', $item);
							}
							return $this->base_url . '/' . ltrim($item, '/');
						}
						return $item;
					})
					->groupBy(function ($item, $key){
						$extentions = ['.css' => 'css', '.js' => 'js', '.ico' => 'images', '.jpg' => 'images', '.jpeg' => 'images', '.gif' => 'images', '.png' => 'images', '.avi' => 'movies', '.mp3' => 'voices', '.mp4' => 'movies', '.html' => 'html'];

						if (! filter_var($item, FILTER_VALIDATE_URL)) {
							return 'invalidUrl';
						}

						if (str_contains($item, 'aparat.com')) {
							return 'aparat';
						}

						// detect url by request url
						if ($this->detectUrlByRequest) {
							$request = new RequestHead($item);
							if ($request->getStatusCode() == 200){
								$content = $request->getContentType();							
								if ($content == 'text/html'){
									return 'html';
								}
								if ($content == 'application/xml') {
									return 'xml';
								}
								return $content;
							}

							if ($request->getStatusCode() == 404) {
								return 'not/found';
							}

							return 'problem';
						}					

						foreach ($extentions as $ext => $value) {
							if (str_contains($item, $ext)) {
								return $value;
							}
						}		


						return 'orther';

					})
					->toArray();			
		return $this->allLinks = $result;

	}

	public function getVideos() {
		$old_set = $this->first_text;
		$this->first_text = true;
		$reject = ['.jpg', '.jpeg', '.mp3', '.png', '.html'];
		$accept = ['.mp4', '.avi'];
		$videos = $this->getLinks();
		$result = [];

		foreach ($videos as $value) {
			for ($i=0; $i < count($reject); $i++) { 
				if (ends_with($value, $reject[$i])) {
					continue(2);
				}
			}

			for ($j=0; $j < count($accept); $j++) { 
				if (ends_with($value, $accept[$j])) {
					$result[] = $value;
				}				
			}

			
			
		}
		//dd($result);
		//dup


		$this->first_text = $old_set;
		return $videos;
	}

	public function getLinks()
	{
		$old_set = $this->first_text;
		$this->first_text = true;
		$links = $this->getHtmlParser('//a/attribute::href | //a/attribute::data-href');
		foreach ($links as $key => $value) {
			if (! starts_with($value, 'http')) {
				if (str_contains($value, '../')){
					$value =str_replace('../', '', $value);
				}
				$links[$key] = $this->base_url . '/' . ltrim($value, '/');
			}
		}

		$this->first_text = $old_set;
		return $links;
	}

	public function removeContent()
	{
		foreach ($this->removeContent as $value) {
	
			$this->crawler->filterXPath($value)
						->each(function(DomCrawler $crawler){
							foreach ($crawler as $node) {
								$node->parentNode->removeChild($node);
							}
						});
		}

		return $this->crawler;
	}

	public function getCrawler()
	{
		return $this->crawler;
	}

	public function setCrawler($html)
	{
		$this->crawler = new DomCrawler($html);
	}

	public function getHtml()
	{
		return $this->crawler->html();
	}

	public function getRemoveContent()
	{
		return $this->removeContent;
	}

	public function setRemoveContent($var)
	{
		if ($var){
			array_push($this->removeContent, $var);			
		}
	}

	public function getData($key = '')
	{
		$data = $this->data->toArray();
		if ($key != "" && is_array($data)) {
			if (array_has($data, $key)) {
				return array_get($data, $key);
			}
			return null;
		}
		return $data;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setContent($var)
	{
		if ($var){
			$this->content = array_push($this->content, $var);
		}
	}

	public function parseContent ($content){

		$data = [];
		//$content = $this->content;
		if (is_string($content)) {
			return $this->getHtmlParser($content, '');
		}

		foreach ($content as $key => $value) {
			if(is_int($key)){
				// call
				$data[$key] = $this->getHtmlParser($value, '');
			} else {
				if(is_string($value)){
					// call
					$data[$key] = $value;
				} elseif(is_array($value)) {
					$data[$key] = $this->parseContent($value);
				}
			}
		}

		return $data;
	}

	public function _parseContent($content){
		
		$data = [];

		if (is_string($content)) {

			return $this->getHtmlParser($content, '');
		}

		foreach ($content as $key => $value) {

			if ($key == 'each' && is_array($value)) {
				if (isset($value['parent']) & isset($value['child'])) {
					$child = $value['child'];
					if (is_string($child)) {
						$child = [$child];
					}
					$data[$key] = $this->getHtmlParser($value['parent'], $child);

				} else {
					$data[$key] = null;
				}
				
			} elseif (is_string($value)) {
				//dd($value);
				$data[$key] = $this->getHtmlParser($value, '');

			} elseif (is_array($value)) {
				$data[$key] = $this->_parseContent($value);
			}
		}
		return $data;
	}

	public function content()
	{
		return $this->data = collect($this->_parseContent($this->content));
	}

	public function getHtmlParser($root, $child = null)
	{
		return  $this->crawler 
				->filterXPath($root)
				->each(function(DomCrawler $crawler, $i) use ($child) {
					
					$data = [];
					if (is_array($child)){
						foreach ($child as $key => $value) {
							
							$node = $crawler->filterXPath($value);
							if ($node) {
								foreach ($node as $nodeElement) {
								    $data[$key][] = trim($nodeElement->ownerDocument->saveHTML($nodeElement));
								}
							} else {
								$data[$key] = '';
							}
							//$data[$key] = $html;
							/*if (count($node)){
								if ($this->first_text) {
									$data[$key] = $node->first()->text();
								} elseif ($this->last_text) {
									$data[$key] = $node->last()->text();
								} else {
									$data[$key] = $node;												
								}
							} else {
								$data[$key] = '';
							}*/
						}
						return $data;
					}

					/*if ($this->first_text) {
						return $crawler->first()->text();
					} elseif ($this->last_text) {
						return $crawler->last()->text();
					}*/

					$html = '';
					foreach ($crawler as $domElement) {
					    $html .= $domElement->ownerDocument->saveHTML($domElement);
					}
					return $html;

					//return $crawler;

				});
	}

	public function cEach (DomCrawler $crawler)
	{
		return $crawler->each(function (DomCrawler $crawler, $i) {

			return $crawler;
		});
	}

	public function cText(DomCrawler $crawler)
	{
		return $crawler->text();
	}

	public function cFirst(DomCrawler $crawler)
	{
		return $crawler->first();
	}

	public function cLast(DomCrawler $crawler)
	{
		return $crawler->first();
	}


	private function _siteCrawler($args, $class)
	{
		$class = ucfirst($class);

		if (file_exists(__DIR__ . '/HtmlParser/Lib/' . $class . '.php')) {

			$class = "Hamrahnegar\\Crawler\\HtmlParser\Lib\\" . $class;
			return new $class($args);
			
		}
	}

	public function regex ($html)
	{
		if (is_callable($html)){
			return call_user_func_array($html, [$this->getHtml()]);
		}

		return $html;
	}

	public function stringToArray(&$array_ptr, $key, $value) {

	  $keys = explode('.', $key);

	  // extract the last key
	  $last_key = array_pop($keys);

	  // walk/build the array to the specified key
	  while ($arr_key = array_shift($keys)) {
	    if (!array_key_exists($arr_key, $array_ptr)) {
	      $array_ptr[$arr_key] = array();
	    }
	    $array_ptr = &$array_ptr[$arr_key];
	  }

	  // set the final key
	  $array_ptr[$last_key] = $value;
	}

	public function with($data)
	{

		if (is_string($data)){
			return $this->getData($data);
		}

		if (is_callable($data)){
			return call_user_func_array($data, [$this->data->toArray()]);
		}

		if (is_array($data)) {
			$result = [];
			foreach ($data as $key => $value) {

				if (is_string($value)){
					array_set($result, $value, $this->getData($value));
				}
						
				elseif (is_callable($value)) {					
					if (($getData = $this->getData($key))) {
						$callData = call_user_func_array($value, [$getData]);
						array_set($result, $key, $callData);
					} else {
						array_set($result, $key, []);
					}
				}					
				
			}

			return $result;
		}

		return $this->data;
	}

	public function __call ($func_name, $args)
	{
		/*dd($func_name);
		if (is_callable($func_name)){
			dd(call_user_func_array($func_name, $this->getHtml()));
		}*/

		//$args[] = $func_name;
		//return call_user_func_array([$this, '_siteCrawler'], $args);
		
	}

}