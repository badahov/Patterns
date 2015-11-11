<?php

class UnitException extends Exception {}

/**
 * Боевая единица
 **/
abstract class Unit {
    function getComposite() {
        return null;
    }

    //Атакующая сила
    abstract function bombardStrength();    
}

abstract class CompositeUnit extends Unit {
    private $units = [];

    function getComposite() {
        return $this;
    }

    protected function units() {
        return $this->units;
    }

    //Добавление боевой единицы
    function addUnit( Unit $unit ) {
        if( in_array( $unit, $this->units, true ) ) {
            return;
        }
        $this->units[] = $unit;
    }

    //Удаление боевой единицы
    function removeUnit( Unit $unit ) {
        $this->units = array_udiff( $this->units, array( $unit ),
                function( $a, $b ) { return ( $a === $b )?0:1; } );
    }
}

//Стрелец
class Archer extends Unit {
    function bombardStrength() {
        return 4;
    }
}

class Sapper extends Unit {
    function bombardStrength() {
        return 2;
    }
}

//Лазерная пушка
class LaserCannonUnit extends Unit {
    function bombardStrength() {
        return 44;
    }
}

//Армия
class Army extends CompositeUnit {

    //Атакующая сила армии
    function bombardStrength() {        
        $ret = 0;
        foreach( $this->units() as $unit ) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }

}

//Бронетранспортер
class TroopCarrier extends CompositeUnit {

    function addUnit( Unit $unit ) {
        if( $unit instanceof Cavalry ) {
            throw new UnitException("Нельзя помещать лошадь на бронетраспортер");
        }
        parent::addUnit( $unit );
    }

    function bombardStrength() {
        $ret = 0;
        foreach( $this->units() as $unit ) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }

}

class UnitScript {
    static function joinExisting( Unit $newUnit, Unit $occupyingUnit ) {
        $comp;
        if( ! is_null( $comp = $occupyingUnit->getComposite() ) ) {
            $comp->addUnit( $newUnit );
        } else {
            $comp = new Army();
            $comp->addUnit( $occupyingUnit );
            $comp->addUnit( $newUnit );
        }
        return $comp;
    }
}

//Создаем армию
$main_army = new Army();

//Добавим пару боевых единиц
$main_army->addUnit( new Archer() );
$main_army->addUnit( new LaserCannonUnit() );

//Создаем армию
$sub_army = new Army();
$sub_army->addUnit( new Archer() );
$sub_army->addUnit( new Archer() );
$sub_army->addUnit( new Archer() );

$sub_army2 = new Army();
$sub_army2->addUnit( new Sapper() );

$sub_army3 = new TroopCarrier();
$sub_army3->addUnit( new Sapper() );
$sub_army3->addUnit( new Sapper() );
$sub_army3->addUnit( new Archer() );

$sub_army2->addUnit( UnitScript::joinExisting( new Sapper(), new TroopCarrier() ) );

//Собираем армии в одну
$main_army->addUnit( $sub_army );
$main_army->addUnit( $sub_army2 );
$main_army->addUnit( $sub_army3 );

//Все вычисления выполняются за кулисами
print "Атакующая сила: {$main_army->bombardStrength()}\n";
