# zsql\Multiplex.php

A drop in replacement for zsql\Database with support for read/write splitting.

## Usage


## Usage

```php
$database = new \zsql\Multiplex($reader, $writer);


$database->select()
  ->from('tableName')
  ->where('columnName', 'value')
  ->limit(1)
  ->query();
  
$id = $database->insert()
  ->ignore()
  ->into('tableName')
  ->value('columnName', 'value')
  ->value('otherColumnName', 'otherValue')
  ->query();

$database->update()
  ->table('tableName')
  ->set('columnName', 'value')
  ->set('someColumn', new zsql\Expression('NOW()'))
  ->where('otherColumnName', 'otherValue')
  ->limit(1)
  ->query();

$database->delete()
  ->from('tableName')
  ->where('columnName', 'value')
  ->limit(1)
  ->query();
```