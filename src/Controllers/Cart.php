<?php
namespace Cheanizer\Poc\Controllers;

use Cheanizer\Poc\Abstracts\Controller;
use Cheanizer\Poc\Traits\Authentication;
use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;

class Cart extends Controller
{
    use Authentication;

    public function list()
    {
        $this->mustLoggedIn();
        $builder = new MysqlBuilder();
        $query = $builder->select()->setTable('carts')
        ->where()
        ->equals('user_id',$this->user['id'])
        ->end();
        $sql = $builder->write($query);
        $stat = $this->db->prepare($sql);
        $stat->execute($builder->getValues());
        $carts = [];
        while ($cart = $stat->fetchObject()){
            array_push($carts,$cart);
        }

        $this->response(json_encode($carts));
        
    }
}