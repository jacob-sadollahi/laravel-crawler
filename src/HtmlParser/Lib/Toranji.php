<?php

namespace Hamrahnegar\Crawler\HtmlParser\Lib;

use Hamrahnegar\Crawler\Crawler;

class Toranji extends Crawler
{

	protected $first_text = true;
	protected $detectUrlByRequest = false;

	protected $removeContent = [
		//'//section[@class="large-6 large-push-3 medium-8 columns home-col entries"]',
		'//div[@class="small-content"]/a',
	];

	protected $content = [
	
		'root' => [
			'each' => [
				'parent' => '//div[@class="content-area"]/div[@class="content-container"]',
				'child' => [
					'image' => '//div[@class="col-md-3 small-thumb"]/img/attribute::src',
					'link' => '//div[@class="col-md-9 small-body"]/a/attribute::href',
					'title' => '//div[@class="small-title"]/h3',
					'description' => '//div[@class="small-content"]',
					'date' => '//div[@class="meta-container"]/ul/li',
				]
			]
			
		],

		/*'feed' => [
			'each' => [
				'parent' => '//item',
				'child' => [
					'title' => '//title',
					'link' => '//link',
					'description' => '//description',
					'date' => '//pubdate',
				]
			],
			'itme' => '//item'
		]*/

	];

}