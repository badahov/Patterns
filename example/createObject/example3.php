<?php

abstract class Employee {
    protected $name;
    private static $types = [ 'Minion', 'CluedUp', 'WellConnected' ];

    static function recruit( $name ) {
        $num = rand( 1, count( self::$types ) )-1;
        $class = self::$types[$num];
        return new $class( $name );
    }

    function __construct( $name ) {
        $this->name = $name;
    }

    abstract function fire();
}

class WellConnected extends Employee {
    function fire() {
        print "{$this->name}: позвони папику\n";
    }
}

class Minion extends Employee {
    function fire() {
        print "{$this->name}: убери со стола\n";
    }
}

class NastyBoss {
    private $employees = [];

    function addEmployee( Employee $employee ) {
        $this->employees[] = $employee;
    }

    function projectFails() {
        if( count( $this->employees ) > 0 ) {
            $emp = array_pop( $this->employees );
            $emp->fire();
        }
    }
}

class ClueUp extends Employee {
    function fire() {
        print "{$this->name}: вызови адвоката\n";
    }
}

$boss = new NastyBoss();
$boss->addEmployee( Employee::recruit( "Игорь" ) );
$boss->addEmployee( Employee::recruit( "Владимир" ) );
$boss->addEmployee( Employee::recruit( "Мария" ) );