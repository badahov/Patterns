<?php

interface Observable {
   function attach( Observer $observer );
   function detach( Observer $observer );
   function notify();
}

class Login implements SplSubject {

    const LOGIN_USER_UNKNOWN = 1;
    const LOGIN_WRONG_PASS   = 2;
    const LOGIN_ACCESS       = 3;

    private $status          = array();
    private $storage;

    function __construct() {
        $this->storage = new SplObjectStorage();
    }

    function handleLogin( $user, $pass, $ip ) {
        switch ( rand( 1, 3 ) ) {
            case 1:
                $this->setStatus( self::LOGIN_ACCESS, $user, $ip );
                $ret = true;
            break;
            case 2:
                $this->setStatus( self::LOGIN_WRONG_PASS, $user, $ip );
                $ret = false;
            break;
            case 3:
                $this->setStatus( self::LOGIN_USER_UNKNOWN, $user, $ip );
                $ret = false;
            break;
        }
        $this->notify();
        return $ret;
    }

    private function setStatus( $status, $user, $ip ) {
        $this->status = array( $status, $user, $ip );
    }

    function getStatus() {
        return $this->status;
    }

    function attach( SplObserver $observer ) {
        $this->storage->attach( $observer );
    }

    function detach( SplObserver $observer ) {
        $this->storage->detach( $observer );
    }

    function notify() {
        foreach( $this->storage as $obs ) {
            $obs->update( $this );
        }
    }
}

interface Observer {
    function update( Observable $observable );
}

abstract class LoginObserver implements SplObserver {
    private $login;

    function __construct( Login $login ) {
        $this->login = $login;
        $login->attach( $this );
    }

    function update( SplSubject $subject ) {
        if( $subject === $this->login ) {
            $this->doUpdate( $subject );
        }
    }

    abstract function doUpdate( Login $login );
}

class SecurityMonitor extends LoginObserver {    
    function doUpdate( Login $login ) {
        $status = $login->getStatus();        
        if( $status[0] == Login::LOGIN_WRONG_PASS ) {
            // Отправим почту системному администратору
            print __CLASS__ . ":\tОтправка почты системному администратору \n";
        }
    }
}

class GeneralLogger extends LoginObserver {
    function doUpdate( Login $login ) {
        $status = $login->getStatus();
        // Регистрируем подключение в журнал
        print __CLASS__ . ":\tРегистрация в системном журнале\n";
    }
}

class PartnershipTool extends LoginObserver {
    function doUpdate( Login $login ) {
        $status = $login->getStatus();
        // Проверим IP
        // Отправим cookie-файл, если адрес соответствует списку
        print __CLASS__ . ":\tОтправка cookie-файл, если адрес соответствует списку\n";
    }
}

$login = new Login();
( new SecurityMonitor( $login ) );
( new GeneralLogger( $login ) );
( new PartnershipTool( $login ) );

$login->handleLogin( 'Anton', '123', '192.168.0.3' );
