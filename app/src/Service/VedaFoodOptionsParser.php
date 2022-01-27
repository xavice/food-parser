<?php

namespace App\Service;

use App\Entity\ParsedMenu;
use Symfony\Component\DomCrawler\Crawler;

final class VedaFoodOptionsParser extends FoodOptionsParser
{
    protected function crawlHtml(string $html): ParsedMenu {
        $crawler = new Crawler($html);
        $menuItems = $crawler->filter("div[id='menu-denne-menu'] li");

        $content = '';
        $day = date('w');

        foreach ($menuItems as $item) {
            $item = new Crawler($item);
            $content .= $item->filter("h4[class='m-item__title restaurant-menu__dish-name']")->text();
            $content .= ": ";
            $content .= $item->filter("button[data-type='add-dish']")->text() . PHP_EOL;
            $content .= $item->filter("div[class='m-item__description']")->text() . PHP_EOL . PHP_EOL;
        }

        $content = rtrim($content, PHP_EOL);

        if (array_key_exists($day, ParsedMenu::DAYS)) {
            $this->parsedMenu->addMenuToDay($content, $day);
        }

        return $this->parsedMenu;
    }
}
