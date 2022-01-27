<?php

namespace App\MessageHandler;

use App\Message\ParsingRequestedMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ParsingRequestedHandler implements MessageHandlerInterface
{
    public function __invoke(ParsingRequestedMessage $message)
    {
        sleep(random_int(2,9));
    }
}