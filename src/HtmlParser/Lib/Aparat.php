<?php

namespace Hamrahnegar\Crawler\HtmlParser\Lib;

use Hamrahnegar\Crawler\Crawler;

class Aparat extends Crawler
{

	protected $first_text = true;
	protected $detectUrlByRequest = false;

	protected $content = [

		'news' => [
			'video' => [
				'download' => '//div[@class="drop-content"]/ul/li[@data-ec="download"]/a/attribute::href',				
			]
			//'//div[@class="container"]/a/attribute::href'], 
		]		

	];

}