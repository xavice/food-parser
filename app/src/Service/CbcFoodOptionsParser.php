<?php

namespace App\Service;

use App\Entity\ParsedMenu;
use Symfony\Component\DomCrawler\Crawler;

final class CbcFoodOptionsParser extends FoodOptionsParser
{
    protected function crawlHtml(string $html): ParsedMenu {
        $crawler = new Crawler($html);
        $menuItems = $crawler->filter("div[class='dnesne_menu'] div[class='jedlo_polozka']");

        $content = '';
        $day = date('w');

        foreach ($menuItems as $item) {
            $item = new Crawler($item);
            $content .= $item->filter("div[class='left']")->text();
            $content .= ": ";
            $content .= $item->filter("span[class='right']")->text() . PHP_EOL;
        }

        $content = rtrim($content, PHP_EOL);

        if (array_key_exists($day, ParsedMenu::DAYS)) {
            $this->parsedMenu->addMenuToDay($content, $day);
        }

        return $this->parsedMenu;
    }
}
