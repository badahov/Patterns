<?php

abstract class Command {
    abstract function execute( CommandContext $context );
}

class Registry {
    private static $instance;

    private function __construct() {}

    public static function getAccessManager() {
        if( empty( self::$instance ) ) {
            self::$instance = new Registry();
        }
        return self::$instance;
    }

    public function login( $user, $pass ) {
        if( $user === 'bob' ) {

            $user = (object) 'User';
            $user->name  = 'bob';

            return $user;
        }        
    }

    public function getError() {
        return 'Ошибка доступа';
    }

}

class CommandContext {
    private $params = [];
    private $error  = "";

    function __construct() {
        $this->params = $_REQUEST;
    }

    function addParam( $key, $val ) {
        $this->params[$key] = $val;
    }

    function get( $key ) {
        return $this->params[$key];
    }

    function setError( $error ) {
        $this->error = $error;
    }

    function getError() {
        return $this->error;
    }
}

class CommandFactory {
    private static $dir = 'commands';

    static function getCommand( $action = 'Default' ) {
        if(preg_match( '/\W/', $action ) ) {
            throw new Exception( "Недопустимые символы в команде" );
        }
        $class = UCFirst( strtolower( $action ) ) . "Command";

        $file = self::$dir . DIRECTORY_SEPARATOR . "{$class}.php";
        
        if( ! file_exists( $file ) ) {
            throw new CommandNotFoundException( "Файл '$file' не найден" );
        }
        require_once ( $file );
        if( ! class_exists( $class ) ) {
            throw new CommandNotFoundException( "Класс '$class' необнаружен" );
        }
        $cmd = new $class();
        return $cmd;
    }
}

class Controller {
    private $context;

    function __construct() {
        $this->context = new CommandContext();
    }

    function getContext() {
        return $this->context;
    }

    function process() {
        $cmd = CommandFactory::getCommand( $this->context->get('action') );
        if( ! $cmd->execute( $this->context ) ) {
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
$context->addParam( 'action', 'login' );
$context->addParam( 'username', 'bob' );
$context->addParam( 'pass', 'tiddles' );
$controller->process();