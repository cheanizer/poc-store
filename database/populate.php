<?php

/**
 * Randomize filling and seeder data. 
 * 
 */


(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('cli only');
use Faker\Factory;
require_once 'bootsrap.php';
use NilPortugues\Sql\QueryBuilder\Builder\MySqlBuilder;


Class Populator 
{
    protected $faker;
    protected $genericBuilder;
    protected $conn;
    public function __construct($conn)
    {
        $this->faker = Factory::create('id_ID');
        $this->genericBuilder = new MySqlBuilder();
        $this->conn = $conn;
        $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    }
    /**
     * generate product 
     * populate table product.
     * generate using faker to randomize result
     */
    public function product($count = 100)
    {
        for ($i =0;$i<$count;$i++)
        {
            // insert using query builder. 
            $query = $this->genericBuilder->insert()
            ->setTable('products')
            ->setValues([
                'name' =>  $this->faker->word,
                'sku' => $this->faker->swiftBicNumber,
                'stock' => $this->faker->numberBetween(500,4000)
            ]);
            // run statement
            $sql = $this->genericBuilder->writeFormatted($query);
            $this->conn->prepare($sql)->execute($this->genericBuilder->getValues());
        }
    }

    /**
     * seed cart table
     */

     public function cart($transactin = 10)
     {
        for ($i=0;$i<10;$i++)
        {
            // select random user
            $query = "select count(*) as 'count' from users";
            $stmt = $this->conn->query($query);
            $count = $stmt->fetch();
            if ($count['count'] < 1) return false;
            $rand = $this->faker->numberBetween(1,$count['count']);
            $userQuery = $this->genericBuilder->select()
            ->setTable('users')
            ->where()
            ->equals('id',$rand)
            ->end();

            $userQuery->limit(0,1);
            $sql = $this->genericBuilder->write($userQuery);
            $value = $this->genericBuilder->getValues();
            $sth = $this->conn->prepare($sql);
            $sth->execute($this->genericBuilder->getValues());
            $user = $sth->fetch();
            
            // select random product
            $query = "select count(*) as 'count' from products";
            $stmt = $this->conn->query($query);
            $count = $stmt->fetch();
            $rand = $this->faker->numberBetween(1,$count['count']);
            $productQuery = $this->genericBuilder->select()
            ->setTable('products')
            ->limit(0,1)
            ->where()
            ->equals('id',$rand)
            ->end();
            
            $sql = $this->genericBuilder->write($productQuery);
            $value = $this->genericBuilder->getValues();
            $stat = $this->conn->prepare($sql);
            $stat->execute($value);
            $product = $stat->fetch();
            
            // emulate customer put thing into chart.
            for ($j=0;$j<rand(3,9);$j++)
            {
                // avoid user put product in chart twice or more
                $sql = "select * from carts where user_id = ? and product_id = ? limit 1";
                $stm = $this->conn->prepare($sql);
                $stm->execute([$user['id'],$product['id']]);
                $cart = $stm->fetch();
                if ($cart) continue;
                $insert = $this->genericBuilder->insert()
                ->setTable('carts')
                ->setValues([
                    'product_id' => $product['id'],
                    'user_id' => $user['id'],
                    'amount' => floor((rand(10,30) / 100) * $product['stock'])
                ]);
                $sql = $this->genericBuilder->writeFormatted($insert);
                $this->conn->prepare($sql)->execute($this->genericBuilder->getValues());
            }
        }
     }

     /**
      * seed user table 
      */

    public function user($count = 50)
    {
        //loop based on count
        for ($i=0;$i<$count;$i++)
        {
            $query = $this->genericBuilder->insert()
            ->setTable('users')
            ->setValues([
                'email' => $this->faker->email,
                'name' => $this->faker->name,
                'password' => sha1('1234')
            ]);

            $sql = $this->genericBuilder->writeFormatted($query);
            $this->conn->prepare($sql)->execute($this->genericBuilder->getValues());
        }
        
    }
    
}

$populator = new Populator($conn);

// populate user table
$populator->user();

// populate product table
$populator->product();

// populate cart 
$populator->cart();

