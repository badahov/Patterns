<?php

class Request_1
{
}

class Request_2
{
}

class RegistryСlassic
{
    private static $instance;
    private $values = [];

    private function __construct()
    {
    }

    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function get($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }

    function set($key, $value)
    {
        $this->values[$key] = $value;
    }
}

$reg = RegistryСlassic::instance();
$reg->set('request', new Request_1);

$reg = RegistryСlassic::instance();
print_r($reg->get('request'));

// ************************************************** //
// ************************************************** //
// ************************************************** //

abstract class Registry
{
    abstract protected function get($key);

    abstract protected function set($key, $val);
}

class RequestRegistry extends Registry
{
    private $values = [];
    private static $instance;

    private function __construct()
    {
    }

    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function get($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }

    protected function set($key, $value)
    {
        $this->values[$key] = $value;
    }

    static function getRequest()
    {
        return self::instance()->get('request');
    }

    static function setRequest(Request_2 $request)
    {
        return self::instance()->set('request', $request);
    }
}

RequestRegistry::setRequest(new Request_2);
print_r(RequestRegistry::getRequest());

class SessionRegistry extends Registry
{
    private static $instance;

    private function __construct()
    {
        //session_start();
    }

    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function get($key)
    {
        if (isset($_SESSION[__CLASS__][$key])) {
            return $_SESSION[__CLASS__][$key];
        }
        return null;
    }

    function set($key, $value)
    {
        $_SESSION[__CLASS__][$key] = $value;
    }

    static function getComplex()
    {
        return self::instance()->get('complex');
    }

    static function setComplex(Complex $complex)
    {
        return self::instance()->set('complex', $complex);
    }
}

class Complex
{
}

SessionRegistry::setComplex(new Complex);
print_r(SessionRegistry::getComplex());

class ApplicationRegistry extends Registry
{
    private static $instance;
    private $freezedir = "data";
    private $values = [];
    private $mtimes = [];

    private function __construct()
    {
    }

    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function get($key)
    {
        $path = $this->freezedir . DIRECTORY_SEPARATOR . $key;
        if (file_exists($path)) {
            clearstatcache();
            $mtime = filemtime($path);
            if (!isset($this->mtimes[$key])) {
                $this->mtimes[$key] = 0;
            }
            if ($mtime > $this->mtimes[$key]) {
                $data = file_get_contents($path);
                $this->mtimes[$key] = $mtime;
                return ($this->values[$key] = unserialize($data));
            }
        }
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }

    protected function set($key, $value)
    {
        $this->values[$key] = $value;
        $path = $this->freezedir . DIRECTORY_SEPARATOR . $key;
        file_put_contents($path, serialize($value));
        $this->mtimes[$key] = time();
    }

    static function getDSN()
    {
        return self::instance()->get('dsn');
    }

    static function setDSN($dsn)
    {
        return self::instance()->set('dsn', $dsn);
    }
}

class DSN
{
}

ApplicationRegistry::setDSN(new DSN);
print_r(ApplicationRegistry::getDSN());

class MemApplicationRegistry extends Registry
{
    private static $instance;
    private $values = [];
    private $id;
    const DSN = 1;

    private function __construct()
    {
        $this->id = @shm_attach(55, 10000, 0600);
        if (!$this->id) {
            throw new Exception("Нет доступа к общей памяти");
        }
    }

    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function get($key)
    {
        return shm_get_var($this->id, $key);
    }

    protected function set($key, $value)
    {
        return shm_put_var($this->id, $key, $value);
    }

    static function getDSN()
    {
        return self::instance()->get(self::DSN);
    }

    static function setDSN($dsn)
    {
        return self::instance()->set(self::DSN, $dsn);
    }
}

try {

    MemApplicationRegistry::setDSN(new DSN);
    print_r(MemApplicationRegistry::getDSN());

} catch (Exception $e) {
    echo 'Error: ', $e->getMessage(), "\n";
}


