<?php

namespace Adapters;

use Adapters\Exceptions\UserAlreadyExistsException;
use Adapters\Exceptions\UserDoesNotExistException;
use Domain\UsersRepositoryInterface;
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
		$sql = "INSERT INTO users (email, name, is_active) ".
				"VALUE (\"" . htmlspecialchars($user->getEmail()) . "\", \"" . htmlspecialchars($user->getName()) . "\", " . (int) $user->isActive() . ")";

		$result = $this->mysql->query($sql);

		if ($result === false) {
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
		$sql = "UPDATE users SET name=\"" . htmlspecialchars($user->getName()) . "\", is_active=\"" . (int) $user->isActive() . "\" WHERE email = \"" . htmlspecialchars($user->getEmail()) . "\"";
		$this->mysql->query($sql);

		if ($this->mysql->affected_rows === 0) {
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

		$rows = $results->fetch_array();

		return new User(
			new Email(htmlspecialchars_decode($rows['email'])),
			htmlspecialchars_decode($rows['name']),
			$rows['is_active']
		);
	}
}
