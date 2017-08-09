<?php

namespace Domain;

use Adapters\Exceptions\UserAlreadyExistsException;
use Adapters\Exceptions\UserDoesNotExistException;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\User;

interface UsersRepositoryInterface
{
	/**
	 * @param User $user
	 *
	 * @return mixed
	 *
	 * @throws UserAlreadyExistsException
	 */
	public function add(User $user);

	/**
	 * @param User $user
	 *
	 * @return mixed
	 *
	 * @throws UserDoesNotExistException
	 */
	public function update(User $user);

	/**
	 * @param Email $email
	 *
	 * @return User
	 *
	 * @throws UserDoesNotExistException
	 */
	public function get(Email $email) : User;
}
