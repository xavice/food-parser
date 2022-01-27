<?php

namespace App\Controller;

use App\Entity\ParsedMenu;
use App\Service\FoodOptionsParser;
use \Symfony\Component\Config\Definition\Exception\Exception as SymfonyException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FoodOptionsController extends AbstractController
{
    #[Route('api/food-options', name: 'food_options')]
    public function index(array $restaurants): Response
    {
        $options = [];
        foreach ($restaurants as $restaurant) {
            $options[] = $this->getOptionsForRestaurant($restaurant);
        }

        return $this->json([
            'day' => ParsedMenu::DAYS[date('w')],
            'options' => $options,
        ]);
    }

    #[Route('api/food-options/{name}', name: 'specific_restaurant_options')]
    public function getSpecificRestaurantOptions(string $name, array $restaurants)
    {
        if (!isset($restaurants[$name])) {
            return $this->json([
                'day' => ParsedMenu::DAYS[date('w')],
                'options' => 'Food options for `%s` are currently not available.',
            ]);

        }
        return $this->json([
            'day' => ParsedMenu::DAYS[date('w')],
            'options' => $this->getOptionsForRestaurant($restaurants[$name]),
        ]);
    }

    /**
     * @throws SymfonyException
     */
    private function getOptionsForRestaurant(array $restaurant): array
    {
        if (!class_exists($restaurant['parser'])) {
            throw new SymfonyException(sprintf('Parser %s does not exists.', $restaurant['parser']));
        }
        /** @var FoodOptionsParser $parser */
        $parser = new $restaurant['parser']();
        if (!$parser instanceof FoodOptionsParser) {
            throw new SymfonyException(sprintf('%s is not an instance of FoodOptionsParser', $parser::class));
        }
        $parsedMenu = $parser->parseMenu($restaurant['url']);

        return [
            'name' => $restaurant['name'],
            'url' => $restaurant['url'],
            'data' => $parsedMenu->getTodayMenu(),
        ];
    }
}
