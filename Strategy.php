<?php

abstract class Question {
    protected $prompt;
    protected $marker;
    
    function __construct( $prompt, Marker $marker ) {
        $this->prompt = $prompt;
        $this->marker = $marker;
    }
    
    function mark( $response ){
        return $this->marker->mark( $response );
    }
}

class TextQuestion extends Question {
    //Выполняются действия, специфичные для текстовых вопросов
}

class AVQuestion extends Question {
    //Выполняются действия, специфичные для мультимедийных вопросов
}

//Пример

abstract class Marker {
    protected $test;

    function __construct( $test ) {
        $this->test = $test;
    }

    abstract function mark( $response );
}

class MarkLogicMarker extends Marker {
    private $engine;
    
    function __construct( $test ) {
        parent::__construct( $test );
        //$this->engine = new MarkParse( $text );
    }
    
    function mark( $response ) {
        //return $this->engine->evaluate( $response );
        return true;
    }
}

class MatchMarker extends Marker {
    function mark( $response ) {
        return ( $this->test == $response );
    }
}

class RegexpMarker extends Marker {
    function mark( $response ) {
        return ( preg_match( $this->test, $response ) );
    }
}

$n = "<br />";

$markers = [ new RegexpMarker( "/Пят./" ), new MatchMarker( "Пять" ), new MarkLogicMarker( '$input equals "Пять"' )];

foreach( $markers as $marker ) {
    print get_class( $marker ) . $n;
    $question = new TextQuestion( "Сколько лучей у кремлевской звезды?", $marker );
    foreach( ["Пять","Четыре"] as $response ) {
        print "Ответ: $response: ";
        if( $question->mark( $response ) ){
            print "Правильно!" . $n;
        } else {
            print "Неверно!" . $n;
        }
    }
}

