<?php
        // دوشنبه, ۲۶ تیر ۹۶, ۱۲:۳۰
       //dd((new DateConvert('دوشنبه, ۲۶ تیر ۹۶, ۱۲:۳۰'))->getDateFormat());

        // dd($this->detectNearDuplicate('test near duplication new for!'));


        // $zoomit = new Zoomit('http://www.zoomit.ir/2017/7/9/188837/60-seconds-123-daily-wrap/');
        dd($zoomit->getData());

    	// news
    	$news = $zoomit->with([	'news.tags',

    							'news.images' => function ($images) {
						    		$data = [];
						    		foreach ($images as $key => $image) {
						    			foreach ($image as $key_1 => $img) {
						    				if (preg_match_all('/\'|\"([^\"|\']*)/is', $img, $matches)) {
							    				array_push($data, $matches[1][0]);
							    			}
						    			}
						    		}
						    		return $data;
						    	},

						    	'news.video' => function ($videos) {
						    		$data = [];
						    		foreach ($videos as $key => $video) {
						    			foreach ($video as $key_1 => $val) {
						    				if (preg_match_all('/\'|\"([^\"|\']*)/is', $val, $matches)) {
							    				array_push($data, $matches[1][0]);
							    			}
						    			}
						    		}
						    		return $data;
						    	},

						    	'news.content' => function ($content) {
						    		return strip_tags($content[0], '<img> <table>');
						    	}]);

    	dd($news);


    	
    	dd( $zoomit->getAllLinks(['aparat' => function ($aparat) {
    		$data = [];
    		foreach ($aparat as $key => $value) {
    			$aparat = new Aparat($value);
		    	$aparat->content();
		    	$data[$key] = $aparat->getData('news.video.download');
		    	foreach ($data[$key] as $key_1 => $val) {
		    		if (preg_match_all('/\'|\"([^\"|\']*)/is', $val, $matches)) {
	    				$data[$key][$key_1] = $matches[1][0];
	    			}
		    	}
    		}
    		return $data;
    	}]));


    	


    	//dd(filter_var('http://aparat.com/zoomit', FILTER_VALIDATE_URL));

    	//$aparat = new Aparat("http://www.aparat.com/v/KtSQl/");
    	$aparat = new Aparat("http://www.zoomit.ir/2017/7/8/188793/60-seconds-122-daily-wrap/");
    	$yjc = new Yjc("http://www.zoomit.ir/2017/7/8/188793/60-seconds-122-daily-wrap/");
    	//$aparat->content();
    	//dd($aparat->getData('news.video.download'), $aparat->getAllLinks());



		//sreturn dd($this->parseA($content));

    	// $digiato = new Digiato('http://digiato.com/article/2017/07/03/%da%86%d8%b1%d8%a7-%d9%88-%da%86%da%af%d9%88%d9%86%d9%87-valve-%d9%87%db%8c%da%86-%d8%b1%d8%a6%db%8c%d8%b3%db%8c-%d9%86%d8%af%d8%a7%d8%b1%d8%af%d8%9f/');
    	//$digiato = new Digiato('http://digiato.com');
    	// $yjc = new Digiato('http://digiato.com/article/2017/02/15/%d9%85%d8%a7%d8%b4%db%8c%d9%86-%d9%be%d8%b1%d9%86%d8%af%d9%87-%d8%ae%d9%88%d8%af%d8%b1%d9%88-%d9%be%d8%b1%d9%86%d8%af%d9%87-%d9%82%db%8c%d9%85%d8%aa-%d9%88-%d9%85%d8%b4%d8%ae%d8%b5%d8%a7%d8%aa-%d8%ae/?video_first');
    	// $digiato = new Digiato('http://digiato.com/article/2017/02/15/%d9%85%d8%a7%d8%b4%db%8c%d9%86-%d9%be%d8%b1%d9%86%d8%af%d9%87-%d8%ae%d9%88%d8%af%d8%b1%d9%88-%d9%be%d8%b1%d9%86%d8%af%d9%87-%d9%82%db%8c%d9%85%d8%aa-%d9%88-%d9%85%d8%b4%d8%ae%d8%b5%d8%a7%d8%aa-%d8%ae/');
    	/*$digiato = new Digiato('https://www.aparat.com/embed/zIH6B?data[rnddiv]=14871511445539686&data[responsive]=yes');
    	$digiato->removeContent();
    	//dd($digiato->getLinks());
    	$data = $digiato->content();
*/
    	//$yjc = new Yjc('http://www.yjc.ir/fa/news/6156627/%D9%86%D8%B8%D8%B1-%D8%AD%D8%A7%D8%AC-%D9%82%D8%A7%D8%B3%D9%85-%D8%B3%D9%84%DB%8C%D9%85%D8%A7%D9%86%DB%8C-%D8%AF%D8%B1%D8%A8%D8%A7%D8%B1%D9%87-%D8%AD%D9%85%D9%84%D9%87-%D9%85%D9%88%D8%B4%DA%A9%DB%8C-%D8%B3%D9%BE%D8%A7%D9%87-%D8%A8%D9%87-%D8%AA%D8%B1%D9%88%D8%B1%DB%8C%D8%B3%D8%AA%E2%80%8C%D9%87%D8%A7-%D9%81%DB%8C%D9%84%D9%85');
    	//$yjc = new Yjc('https://www.tasnimnews.com/fa/media/1396/04/13/1453733/%D9%85%D9%88%D8%B4%D9%86-%DA%AF%D8%B1%D8%A7%D9%81%DB%8C%DA%A9-%DA%A9%D9%84%D8%A7%D9%87-%D8%B3%D9%81%DB%8C%D8%AF%D9%87%D8%A7');
    	//$yjc = new Yjc('http://www.zoomit.ir/2017/7/9/188837/60-seconds-123-daily-wrap/');
    	$yjc->content();
    	//dd($yjc->getAllLinks());

    	// gat all link in page
    	dd($yjc->getAllLinks(['aparat' => function ($aparat){
    		$data = [];
    		//return $aparat;
    		foreach ($aparat as $key => $value) {
    			$aparat = new Aparat($value);
		    	$aparat->content();
		    	$data[] = $aparat->getData('news.video.download');
    		}
    		return $data;
    	}]));
    	// ->gatAllLinks('key');
    	// ->gatAllLinks(['key1', 'key2', 'key3' => function ($key3){return $key3;}]);

    	dd($yjc->getAllLinks(['mp4', 'css' => function ($css){
    		return $css[5];
    	}])/*, $yjc->getVideos()*/);
    	//dd($yjc->getData());

    	//dd($yjc->getData('news.description'));
    	dd(/*$yjc->getData('news.description'), */ $yjc->with([/*'news.description', *//*'news' => function ($news) {
    		return $news;
    	},
    					'news.video' => function ($video){
				    		return $video;
				    	}, */
				    	'news.images.description' => function ($images) {
				    		return $images;
				    	},
				    	'news.tags' => function($tags){
				    		return implode(",", $tags);
				    	}
				    ]
    	));
    	return dd($yjc->content());
    	//$newDigiato = new Digiato('https://www.aparat.com/embed/zIH6B?data[rnddiv]=14871511445539686&data[responsive]=yes');

    	$digiato->removeContent();
    	dd($data = $digiato->content());


    	dd($data, /*$digiato->getLinks(), $digiato->getImages(), *//*$data['video']['link'][0],*/$digiato->regex(function($html){
    		if (preg_match_all('/http.\:\/\/[^\'|\"]*/is', $html, $matches)){
    			$url = $matches[0][0];

    			$aparat = new Aparat($url);
    			dd($aparat->content());
    		}
    		return '';
    	}));
    	
    	return;
    	// $name = 'Yaghoub';
    	$digiato = Crawler::digiato('http://digiato.com');

    	dd($digiato->removeContent());
    	//dd(Crawler::test(), (Crawler::digiato('url'))->removeContent());
    	$name = config('crawler.name');

    	return view('crawler::home.index', compact('name'));