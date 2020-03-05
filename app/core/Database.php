<?php


namespace App\Core;

use App\Core\Settings;

class Database
{
    private static $instance = null;

    private $name;
    private $host;
    private $port;
    private $username;
    private $password;

    public function __construct(){

        $settings = new Settings();
        $config = $settings->getConfig();

        $this->host = $config['database_host'];
        $this->port = $config['database_port'];
        $this->name = $config['database_name'];
        $this->username = $config['database_user'];
        $this->password = $config['database_password'];

        self::$instance = new \MysqliDb ($this->getHost(), $this->getUsername(), $this->getPassword(), $this->getName());

    }

    public static function getInstance()
    {
        if (self::$instance != null) {
            return self::$instance;
        }

        return new self;
    }

    public function getName(){
        return $this->name;
    }
    public function getHost(){
        return $this->host;
    }

    public function getPort(){
        return $this->port;
    }

    public function getUsername(){
        return $this->username;
    }

    public function getPassword(){
        return $this->password;
    }


    private function __clone ()
    {

    }

    private function __wakeup ()
    {

    }
}