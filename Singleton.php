<?php namespace Singleton;

class PreferencesException extends \Exception {}

/**
 * Pattern Singleton
 */
class Preferences {

    /**
     * Массив параметров.
     *
     * @var array
     */
    private $params = [];

    /**
     * Ссылка на объект.
     *
     * @var Singleton\Preferences
     */
    private static $instance;

    /**
     * Закрытый конструктор, запрещает создание объекта Preferences
     * вне класса Preferences
     */
    private function __construct() {}

    /**
     * Создание ссылки на объект Preferences или возврат ссылки
     * на объект Preferences если он уже создан ранее
     * 
     * @return Singleton\Preferencess
     */
    public static function instance() {
        if( empty( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Служит оберткой защищенного метода set
     * 
     * @param mixed $key - ключ параметра (константа)
     * @param mixed $val - параметр
     */
    public static function setProperty( $key, $val ) {
        self::instance()->set( $key, $val );
    }

    /**
     * Служит оберткой защищенного метода get
     * 
     * @param mixed $key
     * @return void
     */
    public static function getProperty( $key ) {
        $pref = self::instance();
        return $pref->get($key);
    }

    /**
     * Добавляет параметры в массив params, выводит исключение если
     * ключь уже занят, запрещая переопределение параметра
     * 
     * @param mixed $key - ключ параметра (константа)
     * @param mixed $val - параметр
     * @throws PreferencesException
     */
    protected function set( $key, $val ) {
        if( empty( $this->params[$key] ) ) {
            $this->params[$key] = $val;
        } else {
            throw new PreferencesException("Ключ <b>{$key}</b> занят, используйте другой.");
        }
    }

    /**
     * Возвращает параметр по ключу
     * 
     * @param mixed $key - ключ параметра
     * @return void
     */
    protected function get( $key ) {
        return $this->params[$key];
    }
}

//Добавление параметра "Иван" в ключ "name"
Preferences::setProperty( "name", "Иван" );

//Вывод добавленного ранее параметра по ключу "name" на печать
print Preferences::getProperty( "name" ) . "<br />";

// Проверка исключений
try {

    //Пробуем переопределить ключ "name"
    Preferences::setProperty( "name", "Ivan" );

} catch ( PreferencesException $e ) {
    echo 'Error: ',  $e->getMessage(), "\n";
}