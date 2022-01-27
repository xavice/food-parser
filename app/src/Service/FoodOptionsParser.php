<?php

namespace App\Service;

use App\Entity\ParsedMenu;

abstract class FoodOptionsParser
{
    protected ParsedMenu $parsedMenu;

    public function __construct()
    {
        $this->parsedMenu = new ParsedMenu();
    }

    public function parseMenu(string $url): ParsedMenu {
        $html = $this->getHtml($url);

        return $this->crawlHtml($html);
    }

    protected function getHtml(string $url): string
    {
        return file_get_contents($url);
    }

    abstract protected function crawlHtml(string $html): ParsedMenu;

    protected function replaceBr(string $content, string $replacement = PHP_EOL): string
    {
        return preg_replace('#<br\s*/?>#i', $replacement, $content);
    }
}
