<?php namespace FactoryMethod;

abstract class ApptEncoder
{
    abstract function encode();
}

class BloggsApptEncoder extends ApptEncoder
{
    function encode()
    {
        return "Данные закодированы в формате BloggsCal \n";
    }
}

abstract class CommsManager
{
    abstract function getHeaderText();

    abstract function getApptEncoder();

    abstract function getFooterText();
}

class BloggsCommsManager extends CommsManager
{
    function getHeaderText()
    {
        return "BloggsCal верхний колонтитул\n";
    }

    function getApptEncoder()
    {
        return new BloggsApptEncoder();
    }

    function getFooterText()
    {
        return "BloggsCal нижний колонтитул\n";
    }
}


$Bloggs = new BloggsCommsManager();

print $Bloggs->getHeaderText();

print $Bloggs->getApptEncoder()->encode();

print $Bloggs->getFooterText();