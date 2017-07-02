<?php

namespace Hamrahnegar\Crawler\Facades;

use Illuminate\Support\Facades\Facade;

class CrawlerFacade extends Facade {

	 protected static function getFacadeAccessor() 
	 { 
	 	return 'crawler'; 
	 }

}