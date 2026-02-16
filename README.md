# ABO generator for PHP

PHP generator for ABO file format (compatible with Czech/Slovak banking standards).

## Installation

```bash
composer require sukovf/abo
```

## Usage

```php
<?php

use snoblucha\Abo\Abo;
use snoblucha\Abo\File;
use snoblucha\Abo\Item;

$date = new \DateTimeImmutable();

$abo = new Abo();
$abo->setClientNumber('222780978');
$abo->setOrganization('Ceska narodni zdravotni pojistovna', true);
$abo->setDate($date);
// $abo->setSecurityKey('123456', '654321');

$file = $abo->addFile(File::TYPE_UHRADA);
$file->setSenderBankCode('0300');
// $file->setBankDepartment('082');

$group = $file->addGroup();
$group->setSenderAccount('122780922');
$group->setDueDate($date);

// Add items
$item1 = new Item('174-1999738514/0300', 2000.5, '2220009813');
$item1->setConstSym('8')
      ->setSpecSym('93653')
      ->setMessage('první část');
$group->addItem($item1);

$item2 = new Item('5152046/0300', 2000, '2220000598');
$item2->setConstSym('8')
      ->setSpecSym('93654');
$group->addItem($item2);

$item3 = new Item('192359658/0300', 2000, '2220000004');
$item3->setConstSym('8')
      ->setSpecSym('93655');
$group->addItem($item3);

$item4 = new Item('174-0346006514/0300', 2000, '2220497222');
$item4->setConstSym('8')
      ->setSpecSym('93656')
      ->setMessage('první část');
$group->addItem($item4);

$item5 = new Item('492732514/0300', 2000, '2220000811');
$item5->setConstSym('8')
      ->setSpecSym('93657');
$group->addItem($item5);

echo '<pre>' . $abo->generate() . '</pre>';
```
