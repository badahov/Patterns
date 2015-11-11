<?php

abstract class Tile {
    abstract function getWealthFactor();
}

class Plains extends Tile {
    private $wealthfactor = 2;
    
    function getWealthFactor() {
        return $this->wealthfactor;
    }
}

abstract class TileDecorator extends Tile {
    protected $tile;
    
    function __construct( Tile $tile ) {
        $this->tile = $tile;
    }
}

class DiamondDecorator extends TileDecorator {
    function getWealthFactor() {
        return $this->getWealthFactor() + 2;
    }
}

class PollutionDecorator extends TileDecorator {
    function getWealthFactor() {
        return $this->getWealthFactor() - 4;
    }
}

$tile = new Plains();
print $tile->getWealthFactor(); // Возвращается 2

$tile = new DiamondDecorator( new Plains() );
print $tile->getWealthFactor(); // Возвращается 4

$tile = new PollutionDecorator( new DiamondDecorator( new Plains() ) );
print $tile->getWealthFactor(); // Возвращается 0

class RequestHelper{}

abstract class ProcessRequest {
    abstract function process( RequestHelper $req );
} 

class MainProcess extends RequestHelper {
    function process( RequestHelper $req ) {
        print __CLASS__ . ": выполнение запроса \n";
    }
}

abstract class DecorateProcess extends ProcessRequest {
    protected $processrequest;

    function __construct( ProcessRequest $pr ) {
        $this->processrequest = $pr;
    }
}

class LogRequest extends DecorateProcess {
    function process( RequestHelper $req ) {
        print __CLASS__ . ": регистрация запроса \n";
        $this->processrequest->process( $req );
    }
}

class AuthenticteRequest extends DecorateProcess {
    function process( RequestHelper $req ) {
        print __CLASS__ . ": аутентификация запроса \n";
        $this->processrequest->process( $req );
    }
}

class StructureRequest extends DecorateProcess {
    function process( RequestHelper $req ) {
        print __CLASS__ . ": упорядочение данных запроса \n";
        $this->processrequest->process( $req );
    }
}

$process = new AuthenticteRequest(
            new StructureRequest(
                new LogRequest(
                    new MainProcess()
            )));

$process->process( new RequestHelper() );