<?php
namespace Cheanizer\Poc\Abstracts;

use Dotenv\Dotenv;

class Controller 
{
    protected $db;
    public function __construct()
    {

    }

    public function setConnection($conn){
        $this->db = $conn;
        return $this;
    }

    public function jsonRequest($name = '')
    {
        $json = file_get_contents('php://input');
        $params = json_decode($json);

        if ($params->{$name}) return $params->{$name};
        return false;
    }

    public function response($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo $data;
    }
}