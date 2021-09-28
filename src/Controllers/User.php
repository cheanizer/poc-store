<?php

namespace Cheanizer\Poc\Controllers;

use Cheanizer\Poc\Abstracts\Controller;
use Cheanizer\Poc\Traits\Authentication;
use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;

Class User extends Controller
{
    use Authentication;

    /**
     * get list user
     */

    public function list()
    {
        $builder = new MysqlBuilder();
        
        $query = "SELECT * FROM users JOIN (
            SELECT MAX(product_id) AS 'product_id', user_id FROM carts GROUP BY user_id
            ) c ON users.id = c.user_id WHERE c.product_id IS NOT NULL";
        $stat = $this->db->prepare($query);
        $stat->execute();
        $users = [];

        while ($user = $stat->fetchObject()){
            array_push($users, $user);
        }

        $this->response(json_encode($users));
    }
}