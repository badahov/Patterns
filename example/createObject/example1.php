<?php

abstract class Employee {
    protected $name;

    function __construct( $name ) {
        $this->name = $name;
    }

    abstract function fire();
}

class Minion extends Employee {
    function fire() {
        print "{$this->name}: убери со стола \n";
    }
}

class NastyBoss {
    private $employees = [];
    
    function addEmployee( $employees ) {
        $this->employees[] = new Minion( $employees );
    }

    function projectFails() {
        if( count( $this->employees ) > 0 ) {
            $emp = array_pop( $this->employees );
            $emp->fire();
        }
    }
}

$boss = new NastyBoss();
$boss->addEmployee( "Игорь" );
$boss->addEmployee( "Владимир" );
$boss->addEmployee( "Мария" );
$boss->projectFails();