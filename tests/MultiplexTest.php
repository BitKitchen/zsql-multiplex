<?php

use \Mock\Mysqli as MockMysqli;

class MultiplexTest extends PHPUnit_Framework_TestCase
{
  protected $useVerboseErrorHandler = false;

  public function __construct($name = NULL, array $data = array(), $dataName = '')
  {
    parent::__construct($name, $data, $dataName);

    if( $this->useVerboseErrorHandler ) {
      $this->setVerboseErrorHandler();
    }
  }

  protected function setVerboseErrorHandler()
  {
    $handler = function($errorNumber, $errorString, $errorFile, $errorLine) {
      echo "ERROR INFO\nMessage: $errorString\nFile: $errorFile\nLine: $errorLine\n";
    };
    set_error_handler($handler);
  }

  protected function databaseFactory()
  {
    $mysqli = new \mysqli();
    $mysqli->connect('localhost', 'zsql', 'nopass', 'zsql');
    return $mysqli;
  }

  protected function multiplexerFactory()
  {
    $reader = $this->databaseFactory();
    $writer = $this->databaseFactory();
    return new \zsql\Multiplex($reader, $writer);
  }

  public function testIntialization()
  {
    $database = $this->multiplexerFactory();
    $this->assertTrue($database->hasReader(), "Has reader");
    $this->assertTrue($database->hasWriter(), "Has writer");
    $this->assertInstanceOf('\\mysqli', $database->getReader(), "Has reader");
    $this->assertInstanceOf('\\mysqli', $database->getWriter(), "Has writer");
  }

  public function testSelectQuery()
  {
    $query = "SELECT * FROM `table`";
    $database = $this->multiplexerFactory();
    $result = $database->query($query);
    $this->assertInstanceOf('\\zsql\\Result', $result);
  }

  public function testInsertQuery()
  {
    $database = $this->multiplexerFactory();
    $insert   = $database->insert()
        ->into('table')
        ->set('varchar', 'hello')
        ->set('double', 1.0)
        ->set('null', null);

    $result = $insert->query();
    $writer = $database->getWriter();
    $reader = $database->getReader();
    $this->assertEquals($database->getInsertId(), $writer->insert_id);
    $this->assertEmpty($reader->insert_id);

  }

}