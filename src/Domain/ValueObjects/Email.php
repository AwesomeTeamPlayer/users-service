<?php

namespace Domain\ValueObjects;

class Email
{
	/**
	 * @var string
	 */
	private $emailAddress;

	/**
	 * @param string $emailAddress
	 */
	public function __construct(string $emailAddress)
	{
		if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === false) {
			throw new \InvalidArgumentException('"' . $emailAddress . '" is not correct email address');
		}

		$this->emailAddress = $emailAddress;
	}

	/**
	 * @return string
	 */
	public function getEmailAddress(): string
	{
		return $this->emailAddress;
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->emailAddress;
	}

}
