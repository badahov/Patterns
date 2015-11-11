<?php

function getProductFileLines( $file ) {
    return file( $file );
}

function getProductObjectFromId( $id, $productname ) {
    return new Product( $id, $productname );
}

function getNameFromLine( $line ) {
    if( preg_match( "/.*-(.*)\s\d+/", $line, $array ) ) {
        return str_replace( '_', ' ', $array[1] );
    }
    return '';
}

function getIDFromLine( $line ) {
    if( preg_match( "/^(\d{1,3})-/", $line, $array ) ) {
        return $array[1];
    }
    return -1;
}

class Product {
    public $id;
    public $name;
    
    function __construct( $id, $name ) {
        $this->id   = $id;
        $this->name = $name;
    }
}

class ProductFacade {
    private $products = [];
    
    function __construct( $file ) {
        $this->file = $file;
        $this->compile();
    }
    
    private function compile() {
        $lines = getProductFileLines( $this->file );
        foreach ( $lines as $lines ) {
            $id = getIDFromLine( $line );
            $name = getNameFromLine( $line );
            $this->products[$id] = getProductObjectFromId( $id, $name );
        }
    }
    
    function getProducts() {
        return $this->products;
    }
    
    function getProduct( $id ) {
        return $this->products[$id];
    }
}

$facade = new ProductFacade( 'test.txt' );
$facade->getProduct( 234 );