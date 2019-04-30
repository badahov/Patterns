<?php

class CommandNotFoundException extends Exception
{
}

/**
 * Class Command
 */
abstract class Command
{
    abstract function execute(CommandContext $context);
}

/**
 * Class Registry
 */
class Registry
{
    private static $instance;

    private function __construct()
    {
    }

    public static function getAccessManager()
    {
        if (empty(self::$instance)) {
            self::$instance = new Registry();
        }
        return self::$instance;
    }

    public function login($user, $pass)
    {
        if ($user === 'bob') {
            $user = (object)'User';
            $user->name = 'bob';

            return $user;
        }
    }

    public function getError()
    {
        return 'Ошибка доступа';
    }
}

/**
 * Class LoginCommand
 */
class LoginCommand extends Command
{
    function execute(CommandContext $context)
    {
        $manager = Registry::getAccessManager();
        $user = $context->get('username');
        $pass = $context->get('pass');
        $user_obj = $manager->login($user, $pass);
        if (is_null($user_obj)) {
            $context->setError($manager->getError());
            return false;
        }
        $context->addParam("user", $user_obj);
        return true;
    }
}
