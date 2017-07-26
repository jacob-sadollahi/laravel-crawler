<?php

namespace Hamrahnegar\Crawler\HtmlParser\Lib;

use Hamrahnegar\Crawler\Crawler;

class Digiato extends Crawler
{

	//protected $crawler;
	protected $first_text = true;
	protected $last_text = false;

	protected $removeContent = [
		//'//section[@class="large-6 large-push-3 medium-8 columns home-col entries"]',
		'//section[@class="large-6 large-push-3 medium-8 columns home-col entries"]//article[@class="home-entry generic clearfix "]//p/span/a',
	];

	protected $content = [
		//'//section[@class="large-6 large-push-3 medium-8 columns home-col entries"]//article[@class="home-entry generic clearfix "]' ,

		//'//div[@class="article-content"]//img/attribute::src',

		'root' => [
			'each' => [
				'parent' => '//section[@class="large-6 large-push-3 medium-8 columns home-col entries"]//article[@class="home-entry generic clearfix "]',
				'child' => [
					'image' => '//figure/a/img/attribute::data-src',
					'link' => '//h1/a/attribute::href',
					'title' => '//h1/a',
					'description' => '//p[@class="show-for-medium"]',
					'date' => '//div[@class="post-meta show-for-medium"]/span',
				]
			],

			'images' => [
				'//img/attribute::src',
				'//img/attribute::data-src'
			]
			
		],

		'news' => [
			/*'//section[@class="large-6 large-push-3 medium-8 columns home-col single-content"]/article[@class="single-entry"]' =>
			[
				'images' => '//div[@class="article-content"]//img/attribute::src',
				'tag' => '//div[@class="tag-list clpsd clearfix collexpand"]/ul',
				'text' => '//div[@class="article-content"]',
			],*/

			'images' => '//div[@class="article-content"]//img/attribute::src',
			'text' => '//div[@class="article-content"]',
			'tags' => '//div[@class="tag-list clpsd clearfix collexpand"]/ul',
		],

		/*'side_bar' => [
			'//div[@class="sidebar-widget"]/figure[@class="card-horizontal thum"]/attribute::data-src' => [
				'image' => '//a/img/attribute::data-src',
				'link' => '//a/attribute::href',
				'title' => '//a',
			]
		],*/

		'video' => [
			'link' => '//div[@class="flex-video"]/div/script/attribute::src', 
		]

	];

	/*public function __construct ($url = '')
	{
		//dd('');
		parent::__construct();
	}*/



}