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

    //Удаление боевой единицы
    function removeUnit( Unit $unit ) {
        $this->units = array_udiff( $this->units, array( $unit ),
                function( $a, $b ) { return ( $a === $b )?0:1; } );
    }

    //Добавление боевой единицы
    function addUnit( Unit $unit ) {
        if( in_array( $unit, $this->units, true ) ) {
            return;
        }
        $this->units[] = $unit;
    }
}

//Стрелец
class Archer extends Unit {
    function bombardStrength() {
        return 4;
    }
}

//Сапер
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
class Army extends Unit {
    private $units = [];

    function addUnit( Unit $unit ) {
        if( in_array( $unit, $this->units, true ) ) {
            return;
        }
        $this->units[] = $unit;
    }

    function removeUnit( Unit $unit ) {
        $this->units = array_udiff( $this->units, array( $unit ),
                function( $a, $b ) { return ( $a === $b )?0:1; } );
    }

    //Атакующая сила армии
    function bombardStrength() {
        $ret = 0;
        foreach( $this->units as $unit ) {
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
        return 0;
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

UnitScript::joinExisting( new Sapper(), new TroopCarrier() )->bombardStrength();

//Добавим вторую армию к первой
$main_army->addUnit( $sub_army );

//Все вычисления выполняются за кулисами
print "Атакующая сила: {$main_army->bombardStrength()}\n";
