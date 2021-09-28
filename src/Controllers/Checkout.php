<?php

namespace Cheanizer\Poc\Controllers;

use Cheanizer\Poc\Abstracts\Controller;
use Cheanizer\Poc\Traits\Authentication;
use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;

class Checkout extends Controller
{
    use Authentication;

    public function checkout()
    {
        //get user login
        $this->mustLoggedIn();

        $product_id = $this->jsonRequest('product');
        foreach ($product_id as $id)
        {
            $product = null;
            $builder = new MySqlBuilder();
            $query = $builder->select()
            ->setTable('products')
            ->where()
            ->equals('id',$id)
                ->end();
            $sql = $builder->write($query);

            $stat = $this->db->prepare($sql);
            $values = $builder->getValues();
            $stat->execute($values);
            $product = $stat->fetch();
            
            if ($product)
            {   
                
                //retrive data cart
                $query = "select * from carts where user_id = ? and product_id = ?";
                $stm = $this->db->prepare($query);
                $stm->execute([$this->user['id'],$product['id']]);
                $cart = $stm->fetch();
                if (! $cart) continue;
                // insert checkout
                $query = $builder->insert()->setTable('checkouts')
                ->setValues([
                    'product_id' => $product['id'],
                    'user_id' => $this->user['id'],
                    'amount' => $cart['amount']
                ]);
                $sql = $builder->write($query);
                $stat = $this->db->prepare($sql);
                $stat->execute($builder->getValues());
                $id = $this->db->lastInsertId();
                if ($id){
                    // deduct product stock
                    $stock = $product['stock'] - $cart['amount'];
                    $query = $builder->update()->setTable('products')
                    ->setValues([
                        'stock' => $stock
                    ])
                    ->where()
                    ->equals('id',$product['id']);
                    $sql = $builder->writeFormatted($query);
                    $stm = $this->db->prepare($sql);
                    $stm->execute($builder->getValues());
                }
            }
        }
    }
}