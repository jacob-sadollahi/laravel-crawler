<?php
namespace Hamrahnegar\Crawler;

class RequestHead {

	private $result;

	public function __construct($url = null) 
	{
		$this->result = $this->request($url);
	}

	public function getHeader ($key, $default = null)
	{
		if ($key){
			if (array_has($this->result, $key)) {
				return array_get($this->result, $key);
			}
			return $default;
		}

		return $this->result;
	}

	public function getStatusCode()
	{
		return $this->getHeader('Status-Code', 0);
	}

	public function getContentLength()
	{
		return $this->getHeader('Content-Length');
	}

	public function getUrl()
	{
		return $this->getHeader('Url');
	}

	public function getContentType()
	{
		if ($this->getHeader('Content')){
			return $this->getHeader('Content');			
		}
		return $this->getHeader('Content-Type');
	}
	
	private function request($url)
	{
		$response = @get_headers($url, true);
		
		if (! $response) {
			return $response;
		}

		if (isset($response[0])) {
			$response['Url'] = $url;
			if (isset($response['Content-Type'])) {
				if (is_string($response['Content-Type'])) {				
					$response['Content-Type'] = trim($response['Content-Type']);
					$response['Content-Type'] = explode(" ", str_replace(";", "", $response['Content-Type']));
				}

				$response['content'] = '';
				foreach ($response['Content-Type'] as $content) {					
					if (strpos($content, '/')) {
						$response['Content'] = $content;
					}					
				}
			}

			if (preg_match('/\d{3}/is', $response[0], $code)) {
				$code = $code[0];
				$response['Status-Code'] = $code;
				if ($code == 200) {
					$response['Url'] = $url;					
					return $response;				
				} elseif ($code == 301 || $code == 302) {					
					if (isset($response['Location'])) {
						return $this->request($response['Location']);
					}
					return $response;					
				} else {
					return $response;						
				}	
			}	
		}
		return false;		
	}

}
