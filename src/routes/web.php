<?php

Route::namespace('Hamrahnegar\Crawler\App\Controllers')

	// ->middleware('crawlerAuth')

	->group(function(){
		// crawler controller
		Route::get('crawler', 'CrawlerController@index');


	});

