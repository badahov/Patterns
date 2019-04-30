<?php

/**
 * Class CommandContext
 */
class CommandContext
{
    private $params = [];
    private $error = "";

    function __construct()
    {
        $this->params = $_REQUEST;
    }

    function addParam($key, $val)
    {
        $this->params[$key] = $val;
    }

    function get($key)
    {
        return $this->params[$key];
    }

    function setError($error)
    {
        $this->error = $error;
    }

    function getError()
    {
        return $this->error;
    }
}

/**
 * Class CommandFactory
 */
class CommandFactory
{
    private static $dir = 'commands';

    static function getCommand($action = 'Default')
    {
        if (preg_match('/\W/', $action)) {
            throw new Exception("Недопустимые символы в команде");
        }
        $class = UCFirst(strtolower($action)) . "Command";

        $file = self::$dir . DIRECTORY_SEPARATOR . "{$class}.php";

        if (!file_exists($file)) {
            throw new CommandNotFoundException("Файл '$file' не найден");
        }

        require_once($file);
        if (!class_exists($class)) {
            throw new CommandNotFoundException("Класс '$class' необнаружен");
        }
        $cmd = new $class();

        return $cmd;
    }
}

/**
 * Class Controller
 */
class Controller
{
    private $context;

    function __construct()
    {
        $this->context = new CommandContext();
    }

    function getContext()
    {
        return $this->context;
    }

    function process()
    {
        $cmd = CommandFactory::getCommand($this->context->get('action'));
        if (!$cmd->execute($this->context)) {
            echo "Обработка ошибки: ";
            echo $this->context->getError();;
        } else {
            //Все прошло успешно
            echo "Отображаем результат";
        }
    }
}

$controller = new Controller();
// Эмулируем запрос пользователя
$context = $controller->getContext();
$context->addParam('action', 'login');
$context->addParam('username', 'bob');
$context->addParam('pass', 'tiddles');
$controller->process();