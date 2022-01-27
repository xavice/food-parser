<?php

namespace App\Controller;

use App\Message\ParsingRequestedMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MessengerConttroller extends AbstractController
{
    #[Route('/mgs/dispatch', name: 'send_parsing_request')]
    public function sendParsingRequest(MessageBusInterface $bus)
    {
        $bus->dispatch(new ParsingRequestedMessage('Veranda'));

        return new Response('Parsing request has been submitted');
    }
}