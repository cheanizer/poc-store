<?php

/**
 * Randomize filling and seeder data. 
 * 
 */


(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('cli only');
use Faker\Factory;
require_once 'bootsrap.php';
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;


Class Populator 
{
    protected $faker;
    protected $genericBuilder;
    protected $conn;
    public function __construct($conn)
    {
        $this->faker = Factory::create('id_ID');
        $this->genericBuilder = new GenericBuilder();
        $this->conn = $conn;
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
            $query = "select count(*) as 'count' from users";
            $stmt = $this->conn->query($query);
            $count = $stmt->fetch();
            
            
            $user = $this->genericBuilder->select()
            ->setTable('users')
            ->setColumn(['id'])
            ->limit(1)
            ->where()
            ->equals('id',$this->faker->numberBetween(0,$count['count']))
            ->end();
            
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
$populator->cart();
// populate user table
//$populator->user();

// populate product table
//$populator->product();

