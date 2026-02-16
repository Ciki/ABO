<?php

declare(strict_types=1);

namespace snoblucha\Abo\Tests;

use PHPUnit\Framework\TestCase;
use snoblucha\Abo\Item;

final class ItemTest extends TestCase
{
	public function testGenerateBasic(): void
	{
		$item = new Item('1234567890/0800', 100.50, '1234567890');
		$generated = $item->generate(true);

		$this->assertStringContainsString('1234567890 10050 1234567890 08000000', $generated);
	}


	public function testGenerateWithMessage(): void
	{
		$item = new Item('1234567890/0800', 100.50, '1234567890');
		$item->setMessage('Test message');
		$generated = $item->generate(true);

		$this->assertStringContainsString('AV:Test message', $generated);
		$this->assertStringNotContainsString('NP:', $generated);
	}


	public function testGenerateWithRecipientName(): void
	{
		$item = new Item('1234567890/0800', 100.50, '1234567890');
		$item->setRecipientName('John Doe');
		$generated = $item->generate(true);

		$this->assertStringContainsString('NP:John Doe', $generated);
		$this->assertStringNotContainsString('AV:', $generated);
	}


	public function testGenerateWithMessageAndRecipientName(): void
	{
		$item = new Item('1234567890/0800', 100.50, '1234567890');
		$item->setMessage('Test message');
		$item->setRecipientName('John Doe');
		$generated = $item->generate(true);

		$this->assertStringContainsString('AV:Test message NP:John Doe', $generated);
	}


	public function testRecipientNameTruncationAndAscii(): void
	{
		$item = new Item('1234567890/0800', 100.50, '1234567890');
		// 35 chars of 'x'
		$longName = str_repeat('x', 40);
		$item->setRecipientName($longName);
		$generated = $item->generate(true);

		$expected = 'NP:' . str_repeat('x', 35);
		$this->assertStringContainsString($expected, $generated);

		$npPos = strpos($generated, 'NP:');
		$npPart = substr(trim($generated), $npPos + 3);
		$this->assertEquals(35, strlen($npPart));
	}
}
