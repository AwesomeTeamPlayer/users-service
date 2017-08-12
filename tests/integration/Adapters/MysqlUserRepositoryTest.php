<?php

namespace Adapters;

use Domain\ValueObjects\Email;
use Domain\ValueObjects\User;
use mysqli;
use PHPUnit\Framework\TestCase;

class MysqlUserRepositoryTest extends TestCase
{
	/**
	 * @var mysqli
	 */
	private $mysqli;

	public function setUp()
	{
		$this->mysqli = new mysqli('127.0.0.1', 'root', 'root', 'testdb', 13306);
		$this->mysqli->query('CREATE TABLE users ( id int NOT NULL AUTO_INCREMENT, email varchar(255) NOT NULL, name varchar(255) NOT NULL, is_active BOOLEAN default true NOT NULL, PRIMARY KEY (id));');
		$this->mysqli->query('CREATE UNIQUE INDEX users_unique_index ON users (email);');
	}

	public function tearDown()
	{
		$this->mysqli->query('DROP TABLE users;');
		$this->mysqli->close();
	}

	public function test_add()
	{
		$repository = new MysqlUserRepository($this->mysqli);
		$repository->add(new User(new Email('email@domain.com'), 'Name', true));

		$result = $this->mysqli->query("SELECT * FROM users WHERE email = 'email@domain.com';");
		$this->assertEquals(
			[
				'email' => 'email@domain.com',
				'name' => 'Name',
				'is_active' => '1',
				'id' => 1,
			],
			$result->fetch_assoc()
		);
	}

	/**
	 * @expectedException \Adapters\Exceptions\UserAlreadyExistsException
	 */
	public function test_add_the_same_email_twice()
	{
		$repository = new MysqlUserRepository($this->mysqli);
		$repository->add(new User(new Email('email@domain.com'), 'Name', true));
		$repository->add(new User(new Email('email@domain.com'), 'Name 2', false));
	}

	/**
	 * @expectedException \Adapters\Exceptions\UserDoesNotExistException
	 */
	public function test_update_when_user_does_not_exist()
	{
		$repository = new MysqlUserRepository($this->mysqli);
		$repository->update(new User(new Email('email@domain.com'), 'Name', true));
	}

	public function test_update_user()
	{
		$repository = new MysqlUserRepository($this->mysqli);
		$repository->add(new User(new Email('email@domain.com'), 'Name', true));
		$repository->update(new User(new Email('email@domain.com'), 'Name 2', false));

		$result = $this->mysqli->query("SELECT * FROM users WHERE email = 'email@domain.com';");
		$this->assertEquals(
			[
				'email' => 'email@domain.com',
				'name' => 'Name 2',
				'is_active' => '0',
				'id' => 1,
			],
			$result->fetch_assoc()
		);
	}

	/**
	 * @expectedException \Adapters\Exceptions\UserDoesNotExistException
	 */
	public function test_get_when_user_does_not_exist()
	{
		$repository = new MysqlUserRepository($this->mysqli);
		$repository->get(new Email('email@domain.com'));
	}

	public function test_get_when_user_exists()
	{
		$repository = new MysqlUserRepository($this->mysqli);
		$repository->add(new User(new Email('email@domain.com'), 'Name', true));
		$this->assertEquals(
			new User(
				new Email('email@domain.com'),
				'Name',
				true
			),
			$repository->get(new Email('email@domain.com'))
		);
	}
}
