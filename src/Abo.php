<?php

namespace snoblucha\Abo;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use snoblucha\Abo\Account\File;

class Abo
{
	const HEADER = 'UHL1';


	/** @var File[] */
	private array $items = [];
	private ?string $organization = null;
	private ?string $date = null;
	private ?string $clientNumber = null;
	private ?string $securityCodeFixedPart = null;
	private ?string $securityCodeSecretPart = null;


	public function __construct(string $organization = "")
	{
		$this->setOrganization($organization);
		$this->setDate();
	}


	/**
	 * Set the sender organization name. Less then 20 chars.
	 */
	public function setOrganization(string $organization, bool $truncate = false)
	{
		$maxLen = 20;
		if (strlen($organization) > $maxLen) {
			if ($truncate) {
				$organization = substr($organization, 0, $maxLen);
			} else {
				throw new InvalidArgumentException("Parameter \$organization must be max $maxLen characters long");
			}
		}
		$this->organization = strtoupper($organization);
		return $this;
	}


	/**
	 * Optional part of the header. Set the Fixed key part and security code.
	 * @param string $fixed 6 numbers
	 * @param string $secret 6 numbers
	 */
	public function setSecurityKey(string $fixed, string $secret): self
	{
		if (!is_numeric($fixed) || !is_numeric($secret) || strlen($fixed) !== 6 || strlen($secret) !== 6) {
			throw new InvalidArgumentException('Parameters $fixed and $secret must be numeric strings of length 6!');
		}
		$this->securityCodeFixedPart = $fixed;
		$this->securityCodeSecretPart = $secret;
		return $this;
	}


	public function setDate(?DateTimeInterface $date = null): self
	{
		if ($date == null) {
			$date = new DateTimeImmutable();
		}
		$this->date = $date->format('dmy');
		return $this;
	}


	public function setClientNumber(string $number): self
	{
		$len = 10;
		if (!is_numeric($number) || strlen($number) > $len) {
			throw new InvalidArgumentException("Parameter \$number must be numeric string of max length $len!");
		}
		$this->clientNumber = str_pad($number, $len, '0', STR_PAD_LEFT);
		return $this;
	}


	public function addAccountFile($type = File::TYPE_UHRADA): File
	{
		$item = new File($type);
		$this->items[] = $item;
		$item->setNumber(count($this->items));
		return $item;
	}


	/**
	 * Get the account files
	 * @return File[]
	 */
	public function getFiles(): array
	{
		return $this->items;
	}


	public function generate(): string
	{
		$res = sprintf("%4s%6s%-20s%010d%03d%03d", self::HEADER, $this->date, $this->organization, $this->clientNumber, 1, 1 + count($this->items));
		if ($this->securityCodeSecretPart) {
			$res .= sprintf("%06d%06d", $this->securityCodeFixedPart, $this->securityCodeSecretPart);
		}
		$res .= "\r\n";

		foreach ($this->items as $item) {
			$res .= $item->generate();
		}

		return $res;
	}


	public static function formatAccountNumber(string $number, string $prefix = null): string
	{
		$res = '';
		if ($prefix) {
			if (!is_numeric($prefix) || strlen($prefix) > 6) {
				throw new InvalidArgumentException('Parameter $prefix must be numeric string of max length 6!');
			}
			$res .= $prefix . '-';
		}
		if (!is_numeric($number) || strlen($prefix) > 10) {
			throw new InvalidArgumentException('Parameter $number must be numeric string of max length 10!');
		}

//		$res .= sprintf('%010d', $number);
		$res .= $number;
		return $res;
	}


}