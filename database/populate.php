<?php
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
            $query = $this->genericBuilder->insert()
            ->setTable('products')
            ->setValues([
                'name' =>  $this->faker->word,
                'sku' => $this->faker->swiftBicNumber,
                'stock' => $this->faker->numberBetween(500,4000)
            ]);

            $sql = $this->genericBuilder->writeFormatted($query);
            $this->conn->prepare($sql)->execute($this->genericBuilder->getValues());
        }
    }

    
}

$populator = new Populator($conn);
$populator->product();

