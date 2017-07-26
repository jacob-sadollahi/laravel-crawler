<?php

namespace Hamrahnegar\Crawler\HtmlParser\Lib;

use Hamrahnegar\Crawler\Crawler;

class Yjc extends Crawler
{

	protected $first_text = true;

	protected $detectUrlByRequest = false;

	protected $content = [

		'root' => [],

		'news' => [

			'description' => '//div[@class="news_body_con"]/div[@class="subtitle"]',

			'images' => [
				'description' => '//div[@class="news_body_con"]/div[@class="subtitle"]//img/attribute::src',
				'//div[@class="container-fluid main_page_photo_news"]//div[@class="body"]//img/attribute::src',
				'all' => '//img/attribute::src'

			],

			'tags' => '//div[@class="tags_container"]//div[@class="tag_items"]/a',

			'video' => [
				'link' => '//div[@class="container"]/a/attribute::href', 
			]

		],

		

	];

}