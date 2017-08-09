<?php

namespace Adapters;

use Adapters\Exceptions\UserAlreadyExistsException;
use Adapters\Exceptions\UserDoesNotExistException;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\User;
use mysqli;

class MysqlUserRepository implements UsersRepositoryInterface
{

	/**
	 * @var mysqli
	 */
	private $mysql;

	/**
	 * @param mysqli $mysql
	 */
	public function __construct(mysqli $mysql)
	{
		$this->mysql = $mysql;
	}

	/**
	 * @param User $user
	 *
	 * @return mixed
	 *
	 * @throws UserAlreadyExistsException
	 */
	public function add(User $user)
	{
		$sql = "INSERT INTO users ('email', 'name', 'is_active') 
				VALUE ('" . htmlspecialchars($user->getEmail()) . "', '" . htmlspecialchars($user->getName()) . "', " . $user->isActive() . ")";

		if ($this->mysql->query($sql) === false) {
			throw new UserAlreadyExistsException();
		}
	}

	/**
	 * @param User $user
	 *
	 * @return mixed
	 *
	 * @throws UserDoesNotExistException
	 */
	public function update(User $user)
	{
		$sql = "UPDATE users SET ('name'='" . htmlspecialchars($user->getName()) . "', 'is_active'=" .  $user->isActive(). ") WHERE email = '" . $user->getEmail() . "'";

		if ($this->mysql->query($sql) === false) {
			throw new UserDoesNotExistException();
		}
	}

	/**
	 * @param Email $email
	 *
	 * @return User
	 *
	 * @throws UserDoesNotExistException
	 */
	public function get(Email $email): User
	{
		$sqlQuery = "
			SELECT * FROM users WHERE email = '" . htmlspecialchars($email->getEmailAddress()). "';
		";

		$results = $this->mysql->query($sqlQuery);
		if ($results->num_rows === 0) {
			throw new UserDoesNotExistException();
		}

		return new User(
			new Email(htmlspecialchars_decode($results[0]->email)),
			htmlspecialchars_decode($results[0]->name),
			$results[0]->is_active
		);
	}
}
