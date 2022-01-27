<?php

namespace App\Entity;

use \Symfony\Component\Config\Definition\Exception\Exception as SymfonyException;

class ParsedMenu
{
    public const DAYS = [
        1 => 'Pondelok',
        2 => 'Utorok',
        3 => 'Streda',
        4 => 'Štvrtok',
        5 => 'Piatok',
        6 => 'Sobota',
        7 => 'Nedeľa',
    ];

    private array $dailyMenu = [];

    public function addMenuToDay(string $menu, int $day): void
    {
        if (!array_key_exists($day, self::DAYS)) {
            throw new SymfonyException('Invalid day');
        }

        $this->dailyMenu[$day] = $menu;
    }

    public function getTodayMenu(): string
    {
        $currentDay = date('w');

        if (!isset($this->dailyMenu[$currentDay])) {
            return "Sorry, I was not able to find your options for today.";
        }

        return $this->dailyMenu[$currentDay];
    }
}
