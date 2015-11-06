# zsql\Multiplex.php

[![Build Status](https://travis-ci.org/BitKitchen/zsql-multiplex.svg)](https://travis-ci.org/BitKitchen/zsql-multiplex)

A drop in replacement for zsql\Database with support for read/write splitting.

## Usage

```php
$database = new \zsql\Multiplex($reader, $writer);

// select runs against $reader
$database->select()
  ->from('tableName')
  ->where('columnName', 'value')
  ->limit(1)
  ->query();

// insert runs against $writer
$id = $database->insert()
  ->ignore()
  ->into('tableName')
  ->value('columnName', 'value')
  ->value('otherColumnName', 'otherValue')
  ->query();

// update runs against $writer
$database->update()
  ->table('tableName')
  ->set('columnName', 'value')
  ->set('someColumn', new zsql\Expression('NOW()'))
  ->where('otherColumnName', 'otherValue')
  ->limit(1)
  ->query();

// delete runs against $writer
$database->delete()
  ->from('tableName')
  ->where('columnName', 'value')
  ->limit(1)
  ->query();

// ensure that the next query uses the $writer
$insert   = $database->query('SET @num := 1');
$result   = $database->useWriter()
  ->query('SELECT @num')
  ->fetchColumn();
```
