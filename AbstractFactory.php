<?php namespace AbstractFactory;

/**
 * Class ApptEncoder
 *
 * @package AbstractFactory
 */
abstract class ApptEncoder
{
    abstract function encode();
}

/**
 * Class CommsManager
 *
 * @package AbstractFactory
 */
abstract class CommsManager
{
    const APT = 1;
    const TTD = 2;
    const CONTACT = 3;

    abstract function getHeaderText();

    abstract function make($flag_int);

    abstract function getFooterText();
}

/**
 * Class BlogsApptEncoder
 *
 * @package AbstractFactory
 */
class BlogsApptEncoder extends ApptEncoder
{
    function encode()
    {
        return "Данные закодированы в формате BloggsCal \n";
    }
}

/**
 * Class BlogsTtdEncoder
 *
 * @package AbstractFactory
 */
class BlogsTtdEncoder extends ApptEncoder
{
    function encode()
    {
        return "Данные закодированы в формате BlogsTtd \n";
    }
}

/**
 * Class BlogsContactEncoder
 *
 * @package AbstractFactory
 */
class BlogsContactEncoder extends ApptEncoder
{
    function encode()
    {
        return "Данные закодированы в формате BlogsComms \n";
    }
}

/**
 * Class BlogsCommsManager
 *
 * @package AbstractFactory
 */
class BlogsCommsManager extends CommsManager
{
    function getHeaderText()
    {
        return "Blogs верхний колонтитул\n";
    }

    function make($flag_int)
    {
        switch ($flag_int) {
            case self::APT:
                return new BlogsApptEncoder();
                break;
            case self::CONTACT:
                return new BlogsContactEncoder();
                break;
            case self::TTD:
                return new BlogsTtdEncoder();
                break;
        }
    }

    function getFooterText()
    {
        return "Blogs нижний колонтитул\n";;
    }
}


$Blogs = new BlogsCommsManager();

print $Blogs->getHeaderText();

//Три варианта кодировки
print $Blogs->make($Blogs::APT)->encode();
print $Blogs->make($Blogs::CONTACT)->encode();
print $Blogs->make($Blogs::TTD)->encode();

print $Blogs->getFooterText();