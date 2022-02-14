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
            try {
                $options[] = $this->getOptionsForRestaurant($restaurant);
            } catch (\Throwable $e) {
                $options[] = [
                    'name' => $restaurant['name'],
                    'url' => $restaurant['url'],
                    'data' => "Couldn't parse restaurants menu: " . $e->getMessage(),
                ];
            }
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
        try {
            $options = $this->getOptionsForRestaurant($restaurants[$name]);
        } catch (\Throwable $e) {
            $options = [
                'name' => $restaurants[$name]['name'],
                'url' => $restaurants[$name]['url'],
                'data' => "Couldn't parse restaurants menu: " . $e->getMessage(),
            ];
        }
        return $this->json([
            'day' => ParsedMenu::DAYS[date('w')],
            'options' => $options,
        ]);
    }

    #[Route('slack/food-options', name: 'slack_food_options')]
    public function slackResponse(array $restaurants)
    {
        $options = [];
        foreach ($restaurants as $restaurant) {
            try {
                $options[] = $this->getOptionsForRestaurant($restaurant);
            } catch (\Throwable $e) {
                $options[] = [
                    'name' => $restaurant['name'],
                    'url' => $restaurant['url'],
                    'data' => "Couldn't parse restaurants menu: " . $e->getMessage(),
                ];
            }
        }

        $blocks = [];
        foreach ($options as $option) {
            $blocks[] = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => sprintf('*%s*%s```%s```', $option['name'], PHP_EOL, $option['data']),
                ],
            ];
        }

        return $this->json([
            "response_type" => "in_channel",
            'blocks' => $blocks,
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
