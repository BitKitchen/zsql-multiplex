<?php

namespace zsql;

use mysqli;
use zsql\Adapter\MysqliAdapter;
use zsql\QueryBuilder\Query;
use zsql\Result\MysqliResult;

class Multiplex extends MysqliAdapter
{
  /**
   * @param mysqli
   */
  protected $reader;

  /**
   * @param mysqli
   */
  protected $writer;

  /**
   * @var bool
   */
  protected $forceWriter = false;

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
   * @param Query|string $query
   * @param string $resultmode
   * @return MysqliResult|mixed
   */
  public function query($query, $resultmode = MYSQLI_STORE_RESULT)
  {
    /*
     * Swap out connection for writer for non-select queries
     */
    if ( $this->canUseWriter($query) ) {
      $this->setConnection($this->writer);
      $this->useWriter(false);
    } else {
      $this->setConnection($this->reader);
    }

    return parent::query($query, $resultmode);
  }

  /**
   * Sets a flag that can force the next query to use the writer connection if it is available.
   *
   * @param bool $flag
   * @return $this
   */
  public function useWriter($flag = true)
  {
    $this->forceWriter = $flag;
    return $this;
  }

  /**
   * Returns true if a query should use the writer mysqli object.
   *
   * @param $query
   * @return bool
   */
  public function canUseWriter($query)
  {
    $allowed = !$this->isSelect($query) || $this->writerForced();
    return  $this->hasWriter() && $allowed;
  }

  /**
   * Returns true if the useWriter function was called.
   *
   * @return bool
   */
  public function writerForced()
  {
    return $this->forceWriter;
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
  public function setWriter(mysqli $writer = null)
  {
    $this->writer = $writer;
  }

  /**
   * Tests for queries that do not modify.
   *
   * @param Query|string $query
   * @return bool
   */
  protected function isSelect($query)
  {
    return  $query instanceof \zsql\Select || preg_match('/^(SELECT|SHOW)/i', trim($query));
  }
}
