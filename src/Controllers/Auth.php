<?php
namespace Cheanizer\Poc\Controllers;

use Cheanizer\Poc\Abstracts\Controller;
use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;
use Firebase\JWT\JWT;
/**
 * auth class. 
 * @JWT
 */
class Auth extends Controller
{
    /**
     * json input email, password
     */
    public function login()
    {
        $email = $this->jsonRequest('email');
        $password = $this->jsonRequest('password');
        
        //get user data

        $query = "select email, password from users where email = ?";
        $statement = $this->db->prepare($query);
        $statement->execute([$email]);
        $user = $statement->fetch();
        if (! $user)
        {
            http_response_code(401);
            return false;
        }
        // match simple sha1 
        if ($user['password'] !== sha1($password))
        {
            //respone exception 200
            echo "salah password";
            return false;
        }

        // expired 
        $expired = time() + (20 * 60);

        $payload = [
            'email' => $user['email'],
            'expired' =>  $expired
        ];

        $access_token = JWT::encode($payload, $_ENV['ACCESS_TOKEN_SECRET']);
        $this->response(json_encode([
            'accessToken' => $access_token,
            'expiry' => date(DATE_ISO8601, $expired)
        ]));
    }
}