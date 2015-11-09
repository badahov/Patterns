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
$boss->addEmployee( "Игорь" );
$boss->addEmployee( "Владимир" );
$boss->addEmployee( "Мария" );
$boss->projectFails();


$boss = new NastyBoss();
$boss->addEmployee( new Minion( "Игорь" ) );
$boss->addEmployee( new ClueUp( "Владимир" ) );
$boss->addEmployee( new Minion( "Мария" ) );
$boss->projectFails();
$boss->projectFails();
$boss->projectFails();