<?php

namespace zsql;

use \mysqli;

class Multiplex extends Database
{
  /**
   * @param Database
   */
  protected $reader;

  /**
   * @param Database
   */
  protected $writer;

  /**
   * @param mysqli $reader
   * @param mysqli $writer
   */
  public function __construct(mysqli $reader, mysqli $writer = null)
  {
    $this->setWriter($writer);
    $this->setReader($reader);

    parent::__construct($reader);
  }

  /**
   * Closes all connections
   */
  public function __destruct()
  {
    if( $this->hasReader() ) {
      $this->getReader()->close();
    }

    if( $this->hasWriter() ) {
      $this->getWriter()->close();
    }
  }

  /**
   * Executes an SQL query
   *
   * @param string|\zsql\Query $query
   * @param string $resultmode
   * @return \zsql\Result|mixed
   */
  public function query($query, $resultmode = MYSQLI_STORE_RESULT)
  {
    /*
     * Swap out connection for writer for non-select queries
     */
    if ( !$this->isSelect($query) && $this->hasWriter() ) {
      $this->setConnection($this->writer);
    }

    else {
      $this->setConnection($this->reader);
    }

    return parent::query($query, $resultmode);
  }

  /**
   * Returns true if a reader object is set.
   *
   * @return bool
   */
  public function hasReader()
  {
    return !empty($this->reader);
  }

  /**
   * Returns true if a writer object is set.
   *
   * @return bool
   */
  public function hasWriter()
  {
    return !empty($this->writer);
  }

  /**
   * Returns the reader mysqli object
   *
   * @return mysqli
   */
  public function getReader()
  {
    return $this->reader;
  }

  /**
   * Returns the reader mysqli object
   *
   * @return mysqli
   */
  public function getWriter()
  {
    return $this->writer;
  }

  /**
   * Sets the reader mysqli object
   *
   * @param mysqli $reader
   */
  public function setReader(mysqli $reader)
  {
    $this->reader = $reader;
  }

  /**
   * Sets the writer mysqli object
   *
   * @param mysqli $writer
   */
  public function setWriter(mysqli $writer)
  {
    $this->writer = $writer;
  }

  /**
   * Tests for queries that do not modify.
   *
   * @param string|\zsql\Query $query
   * @return bool
   */
  protected function isSelect($query)
  {
    return  $query instanceof \zsql\Select
        || is_numeric(stripos($query, 'select'))
        || is_numeric(stripos($query, 'show'))
        ;
  }
}