<?php

namespace Hamrahnegar\Crawler\App\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Hamrahnegar\Crawler\HtmlParser\Lib\Digiato;
use Hamrahnegar\Crawler\HtmlParser\Lib\Aparat;
use Hamrahnegar\Crawler\HtmlParser\Lib\Zoomit;
use Hamrahnegar\Crawler\HtmlParser\Lib\Yjc;
use Hamrahnegar\Crawler\HtmlParser\Lib\Caffecinema;
use Hamrahnegar\Crawler\HtmlParser\Lib\Toranji;
use Hamrahnegar\Crawler\JDateTime;
use Hamrahnegar\Crawler\DateConvert;
use FuzzyWuzzy\Fuzz;
use FuzzyWuzzy\Process;
use Crawler;
use Redis;
use DB;
use App\News;

class CrawlerController extends Controller
{	
    //
    public function index()
    { 	
        //
        $toranji = new Caffecinema('http://caffecinema.com/%D8%A8%D8%B1%D8%A7%DB%8C-%D8%B3%D8%A7%D8%AE%D8%AA-%D9%81%DB%8C%D9%84%D9%85-%D8%AE%D9%88%D8%A8-%D8%A8%D9%87-%DA%86%DB%8C%D8%B2%DB%8C-%D8%A8%DB%8C%D8%B4-%D8%A7%D8%B2-%DA%98%D9%86-%D8%AE%D9%88%D8%A8-%D9%86%DB%8C%D8%A7%D8%B2-%D8%A7%D8%B3%D8%AA-%DB%8C%D8%A7%D8%AF%D8%AF%D8%A7%D8%B4%D8%AA%DB%8C-%D8%AF%D8%B1-%D9%85%D9%88%D8%B1%D8%AF-%D9%81%DB%8C%D9%84%D9%85-%D9%81%D8%B5%D9%84-%D9%86%D8%B1%DA%AF%D8%B3-%D9%86%DA%AF%D8%A7%D8%B1-%D8%A2%D8%B0%D8%B1%D8%A8%D8%A7%DB%8C%D8%AC%D8%A7%D9%86%DB%8C');
        //$toranji->removeContent();
        $toranji->content();
        print_r($toranji->getData());
        dd();



        $toranji = new Toranji('http://toranji.ir/');
        $toranji->removeContent();
        $toranji->content();

        $resultT = $toranji->with(['root.each' => function ($each) {
            $data = [];
            $insert = [];

            foreach ($each as $key => $value) {
                
                if (! count($value)) {
                    continue;
                }

                $percent = $this->detectNearDuplicate($value['title'][0]);

                if ($percent > 80) {
                    continue;
                }


                $data[$key]['title'] = strip_tags($value['title'][0]);
                $insert[$key]['title'] = strip_tags($value['title'][0]);
                $insert[$key]['description'] = strip_tags($value['description'][0]);
                $insert[$key]['thumb_image'] = '';
                $insert[$key]['link'] = $value['link'][0];
                
                if (preg_match_all('/\'|\"([^\"|\']*)/is', $value['link'][0], $matches)) {
                    $insert[$key]['link'] = $matches[1][0];
                }

                $pub_time = time();
                $pub_date = time();
                $date = new DateConvert(strip_tags($value['date'][1]));

                if ($date->getTimestamp() > 0) {
                    $pub_time = $date->getTimestamp();
                    $pub_date = $date->getDateFormat();
                }

                $insert[$key]['pub_time'] = $pub_time;
                $insert[$key]['pub_date'] = $pub_date;
                $insert[$key]['creation_time'] = time();
                $insert[$key]['ng_id'] = 51;
                $insert[$key]['cat_id'] = 5;
                $insert[$key]['feed_id'] = 347;
                $insert[$key]['cnfg'] = '';
            }

            if (count($insert)) {
                //$insert = DB::table('news')->insert($insert);                
            }

            return $data;
        }]);

        // 
        $digiato = new Digiato('http://digiato.com/');
        $digiato->removeContent();
        $digiato->content();
        // remove content

        $resultD = $digiato->with(['root.each' => function ($each) {
            $data = [];
            $insert = [];

            foreach ($each as $key => $value) {

                if (! count($value)) {
                    continue;
                }

                $percent = $this->detectNearDuplicate($value['title'][0]);

                if ($percent > 80) {
                    continue;
                }

                $data[$key]['title'] = strip_tags($value['title'][0]);
                $insert[$key]['title'] = strip_tags($value['title'][0]);
                $insert[$key]['description'] = strip_tags($value['description'][0]);
                $insert[$key]['thumb_image'] = '';
                $insert[$key]['link'] = $value['link'][0];
                
                if (preg_match_all('/\'|\"([^\"|\']*)/is', $value['link'][0], $matches)) {
                    $insert[$key]['link'] = $matches[1][0];
                }

                $pub_time = time();
                $pub_date = time();
                $date = new DateConvert(strip_tags($value['date'][1]));

                if ($date->getTimestamp() > 0) {
                    $pub_time = $date->getTimestamp();
                    $pub_date = $date->getDateFormat();
                }

                $insert[$key]['pub_time'] = $pub_time;
                $insert[$key]['pub_date'] = $pub_date;
                $insert[$key]['creation_time'] = time();
                $insert[$key]['ng_id'] = 55;
                $insert[$key]['cat_id'] = 5;
                $insert[$key]['feed_id'] = 350;
                $insert[$key]['cnfg'] = '';
            }

            if (count($insert)) {
                $insert = DB::table('news')->insert($insert);                
            }

            return $data;
        }]);

    	$zoomit = new Zoomit('http://www.zoomit.ir');
    	$zoomit->content();

        // get root
        $result = $zoomit->with(['root.each' => function ($each) {
            $data = [];
            $insert = [];

            foreach ($each as $key => $value) {

                if (! count($value)) {
                    continue;
                }

                $percent = $this->detectNearDuplicate($value['title'][0]);

                if ($percent > 80) {
                    continue;
                }

                $insert[$key]['title'] = $value['title'][0];
                $insert[$key]['description'] = strip_tags($value['description'][0]);
                $data[$key]['category'] = $value['category'][0];
                $data[$key]['image'] = $value['image'][0];
                $insert[$key]['thumb_image'] = '';
                $insert[$key]['link'] = $value['link'][0];

                if (preg_match_all('/\'|\"([^\"|\']*)/is', $value['image'][0], $matches)) {
                    $data[$key]['image'] = $matches[1][0];
                }
                
                if (preg_match_all('/\'|\"([^\"|\']*)/is', $value['link'][0], $matches)) {
                    $insert[$key]['link'] = $matches[1][0];
                }

                $pub_time = time();
                $pub_date = time();
                $date = new DateConvert($value['date'][0]);

                if ($date->getTimestamp() > 0) {
                    $pub_time = $date->getTimestamp();
                    $pub_date = $date->getDateFormat();
                }

                $insert[$key]['pub_time'] = $pub_time;
                $insert[$key]['pub_date'] = $pub_date;
                $insert[$key]['creation_time'] = time();
                $insert[$key]['ng_id'] = 54;
                $insert[$key]['cat_id'] = 5;
                $insert[$key]['feed_id'] = 349;
                $insert[$key]['cnfg'] = '';
            }

            if (count($insert)) {
                $insert = DB::table('news')->insert($insert);                
            }

            return $data;
        }]);

        if ($count = count(array_get($result, 'root.each')) 
            | $countD = count(array_get($resultD, 'root.each'))
            | $countT = count(array_get($resultT, 'root.each'))
            ) {
            $count += $countD + $countT;

            return "found {$count} new news";
        }

        return "not found news";

    }

    private function detectNearDuplicate($title = '')
    {
        $title = trim($title);
        $percent = 0;
        $check = true;
        $fuzz = new Fuzz();
        $process = new Process($fuzz);
        $allNewsTitle = Redis::lRange('raavi:news:title', 0, 100000);

        foreach ($allNewsTitle as $key => $newsTtitle) {
            if (($percent = $fuzz->ratio($title, $newsTtitle)) > 80 ) {
                break;
            }
        }

        if ($check) {
            Redis::lPush('raavi:news:title', $title);
        }

        return $percent;
    }
}


