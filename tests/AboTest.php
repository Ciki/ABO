<?php

declare(strict_types=1);

namespace snoblucha\Abo\Tests;

use PHPUnit\Framework\TestCase;
use snoblucha\Abo\Abo;
use snoblucha\Abo\File;
use snoblucha\Abo\Item;

final class AboTest extends TestCase
{
	public function testGenerateAbo(): void
	{
		$abo = new Abo('My Organization');
		$abo->setClientNumber('1234567890');
		$abo->setSecurityKey('111111', '222222');

		$file = $abo->addFile(File::TYPE_UHRADA);
		$file->setSenderBankCode('0800');

		$group = $file->addGroup();
		$group->setSenderAccount('987654321', '000000');

		$item = new Item('1234567890/0800', 10.00, '555');
		$item->setRecipientName('Recipient Name');
		$group->addItem($item);

		$generated = $abo->generate();

		$this->assertStringContainsString('UHL1', $generated);
		$this->assertStringContainsString('MY ORGANIZATION', $generated);
		$this->assertStringContainsString('NP:Recipient Name', $generated);
		$this->assertStringContainsString('3 +', $generated);
		$this->assertStringContainsString('5 +', $generated);
	}
}
