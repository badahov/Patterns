<?php

abstract class ArmyVisitor
{
    abstract function visit(Unit $node);

    function visitArcher(Archer $node)
    {
        $this->visit($node);
    }

    function visitCavalry(Cavalry $node)
    {
        $this->visit($node);
    }

    function visitLaserCannonUnit(LaserCannonUnit $node)
    {
        $this->visit($node);
    }

    function visitSapper(Sapper $node)
    {
        $this->visit($node);
    }

    function visitArmy(Army $node)
    {
        $this->visit($node);
    }

    function visitTroopCarrier(TroopCarrier $node)
    {
        $this->visit($node);
    }
}

class UnitException extends Exception
{
}

/**
 * Боевая единица
 **/
abstract class Unit
{

    protected $depth;

    /**
     * Метод сообщает, что объект не является композитом
     *
     * @return null
     */
    function getComposite()
    {
        return null;
    }

    /**
     *
     *
     * @param ArmyVisitor $visitor
     */
    function accept(ArmyVisitor $visitor)
    {
        $method = "visit" . get_class($this);

        echo $method . "<br />";

        $visitor->$method($this);
    }

    /**
     * Записываем глубину ветки в дереве
     *
     * @param int $depth
     */
    protected function setDepth($depth)
    {
        $this->depth = $depth;
    }

    /**
     * Возвращаем глубину ветки в дереве
     *
     * @return int
     */
    function getDepth()
    {
        return $this->depth;
    }

    //Атакующая сила
    abstract function bombardStrength();
}

abstract class CompositeUnit extends Unit
{
    private $units = [];

    function getComposite()
    {
        return $this;
    }

    protected function units()
    {
        return $this->units;
    }

    //Добавление боевой единицы
    function addUnit(Unit $unit)
    {
        foreach ($this->units as $thisunit) {
            if ($unit === $thisunit) {
                return;
            }
        }

        $unit->setDepth($this->depth + 1);
        $this->units[] = $unit;
    }

    function accept(ArmyVisitor $visitor)
    {
        parent::accept($visitor);
        foreach ($this->units as $thisunit) {
            $thisunit->accept($visitor);
        }
    }

    //Удаление боевой единицы
    function removeUnit(Unit $unit)
    {
        $this->units = array_udiff($this->units, array($unit),
            function ($a, $b) {
                return ($a === $b) ? 0 : 1;
            });
    }
}

//Стрелец
class Archer extends Unit
{
    function bombardStrength()
    {
        return 4;
    }
}

class Cavalry extends Unit
{
    function bombardStrength()
    {
        return 3;
    }
}

class Sapper extends Unit
{
    function bombardStrength()
    {
        return 2;
    }
}

//Лазерная пушка
class LaserCannonUnit extends Unit
{
    function bombardStrength()
    {
        return 44;
    }
}

//Армия
class Army extends CompositeUnit
{

    //Атакующая сила армии
    function bombardStrength()
    {
        $ret = 0;
        foreach ($this->units() as $unit) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }

}

//Бронетранспортер
class TroopCarrier extends CompositeUnit
{

    function addUnit(Unit $unit)
    {
        if ($unit instanceof Cavalry) {
            throw new UnitException("Нельзя помещать лошадь на бронетраспортер");
        }
        parent::addUnit($unit);
    }

    function bombardStrength()
    {
        $ret = 0;
        foreach ($this->units() as $unit) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }

}

class UnitScript
{
    static function joinExisting(Unit $newUnit, Unit $occupyingUnit)
    {
        $comp;
        if (!is_null($comp = $occupyingUnit->getComposite())) {
            $comp->addUnit($newUnit);
        } else {
            $comp = new Army();
            $comp->addUnit($occupyingUnit);
            $comp->addUnit($newUnit);
        }
        return $comp;
    }
}

class TextDumpArmyVisitor extends ArmyVisitor
{
    private $text = "";

    function visit(Unit $node)
    {
        $ret = "";
        $pad = 4 * $node->getDepth();
        $ret .= sprintf("%{$pad}s", "");
        $ret .= get_class($node) . ": ";
        $ret .= "Огневая мощь: " . $node->bombardStrength() . "<br />";
        $this->text .= $ret;
    }

    function getText()
    {
        return $this->text;
    }
}

class TaxCollectionVisitor extends ArmyVisitor
{
    private $due = 0;
    private $report = "";

    function visit(Unit $node)
    {
        $this->levy($node, 1);
    }

    function visitArcher(Archer $node)
    {
        $this->levy($node, 2);
    }

    function visitCavalry(Cavalry $node)
    {
        $this->levy($node, 3);
    }

    function visitLaserCannonUnit(LaserCannonUnit $node)
    {
        $this->levy($node, 5);
    }

    private function levy(Unit $unit, $amount)
    {
        $this->report .= "Налог для " . get_class($unit);
        $this->report .= ": $amount<br />";
        $this->due += $amount;
    }

    function getReport()
    {
        return $this->report;
    }

    function getTax()
    {
        return $this->due;
    }
}

//Создаем армию
$main_army = new Army();

//Добавим пару боевых единиц
$main_army->addUnit(new Archer());
$main_army->addUnit(new LaserCannonUnit());

//Создаем армию
$sub_army = new Army();
$sub_army->addUnit(new Archer());
$sub_army->addUnit(new Archer());
$sub_army->addUnit(new Archer());

$sub_army2 = new Army();
$sub_army2->addUnit(new Sapper());

$sub_army3 = new TroopCarrier();
$sub_army3->addUnit(new Sapper());
$sub_army3->addUnit(new Sapper());
$sub_army3->addUnit(new Archer());

$sub_army2->addUnit(UnitScript::joinExisting(new Sapper(), new TroopCarrier()));

//Собираем армии в одну
$main_army->addUnit($sub_army);
$main_army->addUnit($sub_army2);
$main_army->addUnit($sub_army3);

$textdump = new TextDumpArmyVisitor();
$main_army->accept($textdump);
print $textdump->getText();

print "<br />";

$taxcollector = new TaxCollectionVisitor();
$main_army->accept($taxcollector);
print $taxcollector->getReport() . "<br />";
print "Итого: ";
print $taxcollector->getTax() . "<br />";