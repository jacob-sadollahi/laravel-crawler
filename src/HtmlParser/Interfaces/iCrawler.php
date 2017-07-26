<?php
namespace Hamrahnegar\Crawler\HtmlParser\Interfaces;

interface iCrawler
{

    public function getHtmlParser($root, $child);

    public function removeContent();
}