<?php
/**
 * Created by PhpStorm.
 * User: Emad Omar <emad2030@gmail.com>
 * Date: 3/7/2016
 * Time: 5:33 PM
 */

namespace Telegram\Bot\Commands;


class CommandQuestion
{
    protected $name;

    protected $question;

    protected $answer;

    public function __construct($name, $question)
    {
        $this->name = $name;
        $this->question = $question;
    }

    public function answer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

}