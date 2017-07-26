<?php

namespace Hamrahnegar\Crawler\HtmlParser\Lib;

use Hamrahnegar\Crawler\Crawler;

class Zoomit extends Crawler
{

	protected $first_text = true;
	protected $detectUrlByRequest = false;

	protected $content = [

		'root' => [
			'each' => [
				'parent' => '//div[@id="wrapcenter"]/div[@id="latestarticles"]/div[@class="bg-white pad20A pad0T-M"]/div[@class="item-list-row col-xs-pad0LR"]',
				'child' => [
					'image' => '//img/attribute::src',
					'link' => '//h3/a/attribute::href',
					'title' => '//h3/a/text()',
					'description' => '//p/text()',
					'category' => '//div[@class="catgroup hidden-xs"]/ul/li/a/text()',
					'date' => '//span[@class="datelist"]/text()'
				]
			]
			
		],

		'news' => [
			'content' => '//div[@class="article-content"]/div[@itemprop="articleBody"][@class="article-section"]',
			'video' => [],
			'tags' => '//div[@class="article-tag-row"]/div/a[@itemprop="keywords"]/text()',
			'images' => [
				'//div[@itemprop="articleBody"][@class="article-section"]//img/attribute::src',
				'//div[@class="article-content"]/div[@itemprop="image"]/img/attribute::src'
			]
			
		]		

	];

	

}