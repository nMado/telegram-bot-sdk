<?php

namespace Telegram\Bot\Commands;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramOtherException;
use Telegram\Bot\Objects\Update;

/**
 * Class Command.
 *
 *
 * @method mixed replyWithMessage($use_sendMessage_parameters)       Reply Chat with a message. You can use all the sendMessage() parameters except chat_id.
 * @method mixed replyWithPhoto($use_sendPhoto_parameters)           Reply Chat with a Photo. You can use all the sendPhoto() parameters except chat_id.
 * @method mixed replyWithAudio($use_sendAudio_parameters)           Reply Chat with an Audio message. You can use all the sendAudio() parameters except chat_id.
 * @method mixed replyWithVideo($use_sendVideo_parameters)           Reply Chat with a Video. You can use all the sendVideo() parameters except chat_id.
 * @method mixed replyWithVoice($use_sendVoice_parameters)           Reply Chat with a Voice message. You can use all the sendVoice() parameters except chat_id.
 * @method mixed replyWithDocument($use_sendDocument_parameters)     Reply Chat with a Document. You can use all the sendDocument() parameters except chat_id.
 * @method mixed replyWithSticker($use_sendSticker_parameters)       Reply Chat with a Sticker. You can use all the sendSticker() parameters except chat_id.
 * @method mixed replyWithLocation($use_sendLocation_parameters)     Reply Chat with a Location. You can use all the sendLocation() parameters except chat_id.
 * @method mixed replyWithChatAction($use_sendChatAction_parameters) Reply Chat with a Chat Action. You can use all the sendChatAction() parameters except chat_id.
 */
abstract class Command implements CommandInterface
{
    /**
     * The name of the Telegram command.
     * Ex: help - Whenever the user sends /help, this would be resolved.
     *
     * @var string
     */
    protected $name;

    /**
     * @var string The Telegram command description.
     */
    protected $description;

    /**
     * @var Api Holds the Super Class Instance.
     */
    protected $telegram;

    /**
     * @var string Arguments passed to the command.
     */
    protected $arguments;

    /**
     * @var Update Holds an Update object.
     */
    protected $update;

    /**
     * List of this commands questions
     *
     * @var CommandQuestion[]
     */
    private $questions = [];

    /**
     * Get Command Name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Command Name.
     *
     * @param $name
     *
     * @return Command
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Command Description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set Command Description.
     *
     * @param $description
     *
     * @return Command
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Returns Telegram Instance.
     *
     * @return Api
     */
    public function getTelegram()
    {
        return $this->telegram;
    }

    /**
     * Returns Original Update.
     *
     * @return Update
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * Get Arguments passed to the command.
     *
     * @return string
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Returns an instance of Command Bus.
     *
     * @return CommandBus
     */
    public function getCommandBus()
    {
        return $this->telegram->getCommandBus();
    }

    /**
     * Returns an instance of Conversation Base.
     *
     * @return \Telegram\Bot\ConversationBase
     */
    public function getConversationBase()
    {
        return $this->telegram->getConversationBase();
    }

    /**
     * @return CommandQuestion[]
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    public function isConversational() {
        return !empty($this->questions);
    }

    /**
     * {@inheritdoc}
     */
    public function make($telegram, $arguments, $update)
    {
        $this->telegram = $telegram;
        $this->arguments = $arguments;
        $this->update = $update;

        return $this->handle($arguments);
    }

    /**
     * Helper to Trigger other Commands.
     *
     * @param      $command
     * @param null $arguments
     *
     * @return mixed
     */
    protected function triggerCommand($command, $arguments = null)
    {
        return $this->getCommandBus()->execute($command, $arguments ?: $this->arguments, $this->update);
    }

    protected function ask($name, $question)
    {
        if (array_key_exists($name, $this->questions)) {
            throw new TelegramOtherException("A question with the name `" . $name . "` has already been added.");
        }

        $this->questions[$name] = new CommandQuestion($name, $question);

        return $this->questions[$name];
    }

    /**
     * {@inheritdoc}
     */
    abstract public function handle($arguments);

    /**
     * Magic Method to handle all ReplyWith Methods.
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed|string
     */
    public function __call($method, $arguments)
    {
        $action = substr($method, 0, 9);
        if ($action === 'replyWith') {
            $reply_name = studly_case(substr($method, 9));
            $methodName = 'send'.$reply_name;

            if (!method_exists($this->telegram, $methodName)) {
                throw new \BadMethodCallException("Method [$method] does not exist.");
            }

            $chat_id = $this->update->getMessage()->getChat()->getId();
            $params = array_merge(compact('chat_id'), $arguments[0]);

            return call_user_func_array([$this->telegram, $methodName], [$params]);
        }

        throw new \BadMethodCallException("Method [$method] does not exist.");
    }
}
