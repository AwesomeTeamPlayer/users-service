<?php

namespace Domain\ValueObjects;

class User
{
	/**
	 * @var Email
	 */
	private $email;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var bool
	 */
	private $isActive;

	/**
	 * User constructor.
	 * @param Email $email
	 * @param string $name
	 * @param bool $isActive
	 */
	public function __construct(Email $email, $name, $isActive)
	{
		$this->email = $email;
		$this->name = $name;
		$this->isActive = $isActive;
	}

	/**
	 * @return Email
	 */
	public function getEmail(): Email
	{
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->isActive;
	}
}
