<?php

namespace App\Service;

use App\Entity\ParsedMenu;
use Symfony\Component\DomCrawler\Crawler;

final class VerandaFoodOptionsParser extends FoodOptionsParser
{
    protected function crawlHtml(string $html): ParsedMenu {
        $crawler = new Crawler($html);
        $menuItems = $crawler->filter("div[class='day-menu']");

        foreach ($menuItems as $item) {
            $item = new Crawler($item);
            $day = $item->filter("div[class='day'] > span")->innerText();
            $content = $item->filter("div[class='dayly-menu-list']")->html();

            if (in_array($day, ParsedMenu::DAYS, true)) {
                $dayKey = array_search($day, ParsedMenu::DAYS, true);
                $trimmedContent = trim(str_replace("\t", " ", $content));
                $trimmedContent = str_replace('<br>', "", $trimmedContent);
                $this->parsedMenu->addMenuToDay($trimmedContent, $dayKey);
            }
        }

        return $this->parsedMenu;
    }
}
