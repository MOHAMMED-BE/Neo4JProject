<?php
require_once('vendor\autoload.php');
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\TransactionInterface;
use Laudis\Neo4j\Databags\Statement;
use Laudis\Neo4j\Databags\stack;

$DBName = 'neo4j';
$Password = '12345' ;

try{
  $client = ClientBuilder::create()
  ->withDriver('neoPHP', 'bolt://localhost:7687', 
  Authenticate::basic($DBName, $Password)) 
  ->build();


}

catch(PDOException $ex){
  echo ('Error');
}

?>