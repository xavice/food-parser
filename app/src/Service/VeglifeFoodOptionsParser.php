<?php

namespace App\Service;

use App\Entity\ParsedMenu;
use Symfony\Component\DomCrawler\Crawler;

final class VeglifeFoodOptionsParser extends FoodOptionsParser
{
    protected function crawlHtml(string $html): ParsedMenu {
        $crawler = new Crawler($html);
        $menuItems = $crawler->filter("table tbody tr");

        $content = '';
        $day = date('w');

        foreach ($menuItems as $item) {
            $item = new Crawler($item);
            $content .= $item->filter("td")->eq(1)->text() ?? "";
            $content .= ": ";
            $content .= $item->filter("td")->eq(3)->text();
            $content .= PHP_EOL;
        }
        $content = rtrim($content, PHP_EOL);

        if (array_key_exists($day, ParsedMenu::DAYS)) {
            $this->parsedMenu->addMenuToDay($content, $day);
        }

        return $this->parsedMenu;
    }
}
