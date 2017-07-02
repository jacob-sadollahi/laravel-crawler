<?php

namespace Hamrahnegar\Crawler\App\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Crawler;


class CrawlerController extends Controller
{
    //
    public function index(){
    	// $name = 'Yaghoub';
    	dd(Crawler::test());
    	$name = config('crawler.name');

    	return view('crawler::home.index', compact('name'));
    }
}
