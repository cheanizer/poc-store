<?php
namespace Cheanizer\Poc\Traits;

use Exception;
use Firebase\JWT\JWT;

Trait Authentication{
    protected $user;
    public function mustLoggedIn()
    {
        $headers = getallheaders();
        list(, $token) = explode(' ', $headers['Authorization']);
        try {
            $resutst = JWT::decode($token,$_ENV['ACCESS_TOKEN_SECRET'],['HS256']);
            if ($resutst)
            {
                // get user info from token finromation
                $query = "select * from users where email = ?";
                $stat = $this->db->prepare($query);
                $stat->execute([$resutst->email]);
                $res = $stat->fetch();
                if ($res) $this->user = $res;
            }else {
                http_response_code(401);
                die();
            }
        }catch(Exception $e)
        {
            http_response_code(401);
            die();
        }
    }

}