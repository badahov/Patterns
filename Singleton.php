<?php namespace Singleton;

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
     * @var array
     */
    private static $instance;

    /**
     * Закрытый конструктов запрещает создание объекта Preferences
     * вне класса Preferences
     */
    private function __construct() {}

    /**
     * Создание ссылки на объект Preferences или возврат ссылки
     * на объект Preferences если он уже создан ранее
     * 
     * @return object
     */
    public static function instance() {
        if( empty( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Добавляет параметры в массив props, выводит исключение если
     * ключь уже занят запрещая переопределение 
     * 
     * @param mixed $key - ключ параметра (константа)
     * @param mixed $val - параметр
     * @throws Exception
     */
    protected function set( $key, $val ) {
        if( empty( $this->params[$key] ) ) {
            $this->params[$key] = $val;
        } else {
            throw new \Exception("Ключ <b>{$key}</b> занят, используйте другой.");
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

    /**
     * Служит оберткой метода set
     * 
     * @param mixed $key - ключ параметра (константа)
     * @param mixed $val - параметр
     */
    public static function setProperty( $key, $val ) {
        self::instance()->set( $key, $val );
    }

    /**
     * Служит оберткой метода get
     * 
     * @param mixed $key
     * @return void
     */
    public static function getProperty( $key ) {
        $pref = self::instance();
        return $pref->get($key);
    }
}

//Добавление параметра
Preferences::setProperty( "name", "Иван" );

//Вывод добавленного ранее параметра на печать
print Preferences::getProperty( "name" ) . "<br />";

// Проверка исключений
try {

    //Пробуем переопределить ключ "name"
    Preferences::setProperty( "name", "Ivan" );

} catch ( \Exception $e ) {
    echo 'Error: ',  $e->getMessage(), "\n";
}