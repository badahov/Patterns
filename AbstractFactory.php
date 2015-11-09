<?php namespace AbstractFactory;

abstract class ApptEncoder {
    abstract function encode();
}

abstract class CommsManager {

    const APPT    = 1;
    const TTD     = 2;
    const CONTACT = 3;

    abstract function getHeaderText();
    abstract function make( $flag_int );
    abstract function getFooterText();
}

class BloggsApptEncoder extends ApptEncoder {
    function encode() {
        return "Данные закодированы в формате BloggsCal \n";
    }
}

class BloggsTtdEncoder extends ApptEncoder {
    function encode() {
        return "Данные закодированы в формате BloggsTtd \n";
    }
}

class BloggsContactEncoder extends ApptEncoder {
    function encode() {
        return "Данные закодированы в формате BloggsComms \n";
    }
}

class BloggsCommsManager extends CommsManager {
    function getHeaderText() {
        return "Bloggs верхний колонтитул\n";
    }

    function make( $flag_int ) {
        switch ( $flag_int ) {
            case self::APPT:
                return new BloggsApptEncoder();
            break;
            case self::CONTACT:
                return new BloggsContactEncoder();
            break;
            case self::TTD:
                return new BloggsTtdEncoder();
        }
    }

    function getFooterText() {
        return "Bloggs нижний колонтитул\n";;
    }
}


$Bloggs = new BloggsCommsManager();

print $Bloggs->getHeaderText();

print $Bloggs->make( $Bloggs::APPT )->encode();
print $Bloggs->make( $Bloggs::CONTACT )->encode();
print $Bloggs->make( $Bloggs::TTD )->encode();

print $Bloggs->getFooterText();