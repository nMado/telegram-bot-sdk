<?php
/**
 * Created by PhpStorm.
 * User: Emad Omar <emad2030@gmail.com>
 * Date: 3/7/2016
 * Time: 2:11 PM
 */

namespace Telegram\Bot;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\Message;

/**
 * Class ConversationBase
 * @package Telegram\Bot
 */
class ConversationBase
{

    public function hasUnfinishedConversation(Message $message)
    {
        $userId = $message->getFrom()->getId();
        $chatId = $message->getChat()->getId();

        // Check if there's a command cached for the same `userId` and `chatId`
    }

    public function processCommandConversation(Command $command)
    {
        // Call the first question or resume from where the user left
    }

    public function getOngoingCommand(Message $message)
    {
        // Get the last cached command
    }

}