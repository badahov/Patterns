<?php

class Setting {
    static $COMMSTYPE = 'Bloggs';
}

//require_once( 'Setting.php' );

class AppConfig {
    private static $instance;
    private $commsManager;

    private function __construct() {
        //Вызывается только один раз
        $this->init();
    }

    private function init() {
        switch( Setting::$COMMSTYPE ) {
            case 'Mega':
                $this->commsManager = new MegaCommsManager();
            break;
            default:
                $this->commsManager = new AbstractFactory\BloggsCommsManager();
        }
    }

    public static function getInstance() {
        if( empty( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getCommsManager() {
        return $this->commsManager;
    }
}

$commsMgr = AppConfig::getInstance()->getCommsManager();
print $commsMgr->getApptEncoder()->encode();