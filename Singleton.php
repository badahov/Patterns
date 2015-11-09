<?php

/**
 * Pattern Singleton
 */
class Preferences {
    private $props = [];
    private static $instance;

    private function __construct() {}

    public static function getInstance() {
        if( empty( self::$instance ) ) {
            self::$instance = new Preferences();
        }
        return self::$instance;
    }

    public function setProperty( $key, $val ) {
        if( ! isset( $this->props[$key] ) ) {
            $this->props[$key] = $val;
        } else {
            throw new Exception("Key {$key} busy.");
        }
    }

    public function getProperty( $key ) {
        return $this->props[$key];
    }

    public static function setPropertyShort( $key, $val ) {
        $pref = self::getInstance();
        $pref->setProperty( $key, $val );
    }

    public static function getPropertyShort( $key ) {
        $pref = self::getInstance();
        return $pref->getProperty($key);
    }
}

$pref = Preferences::getInstance();
$pref->setProperty( "name", "Ivan" );

Preferences::setPropertyShort( "surname", "Ivanov" );

unset( $pref ); // Удаляем ссылку

$pref2 = Preferences::getInstance();
print $pref2->getProperty( "name" ) . "\n";

print Preferences::getPropertyShort( "surname" ) . "\n";

// Проверка исключений
try {

    $pref = Preferences::getInstance();
    $pref->setProperty( "surname", "Ivanov" );

} catch ( Exception $e ) {
    echo 'Error: ',  $e->getMessage(), "\n";
}

try {

    Preferences::setPropertyShort( "name", "Ivan" );

} catch ( Exception $e ) {
    echo 'Error: ',  $e->getMessage(), "\n";
}