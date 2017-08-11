<?php

namespace Application;

use Adapters\Exceptions\UserDoesNotExistException;
use AwesomeTeamPlayer\Libraries\Adapters\EventsRepositoryInterface;
use AwesomeTeamPlayer\Libraries\Adapters\ValueObjects\Event;
use Domain\UsersRepositoryInterface;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\User;
use PHPUnit\Framework\TestCase;

class UserUpdaterTest extends TestCase
{
	/**
	 * @expectedException \Adapters\Exceptions\UserDoesNotExistException
	 */
	public function test_when_user_does_not_exist()
	{
		$userRepository = $this->getMockBuilder(UsersRepositoryInterface::class)
			->getMock();
		$userRepository->method('update')->willThrowException(new UserDoesNotExistException());

		$eventRepository = $this->getMockBuilder(EventsRepositoryInterface::class)
			->getMock();
		$eventRepository->expects($this->never())->method('push');

		$userAdder = new UserUpdater(
			$userRepository,
			$eventRepository
		);

		$userAdder->update(
			new User(
				new Email('john@domain.com'),
				'John',
				true
			)
		);
	}

	public function test_when_ok()
	{
		$userRepository = $this->getMockBuilder(UsersRepositoryInterface::class)
			->getMock();
		$userRepository
			->method('add')
			->willReturnCallback(function($user){
				$this->assertEquals(
					new User(
						new Email('john@domain.com'),
						'John',
						true
					),
					$user
				);
			});
		$eventRepository = $this->getMockBuilder(EventsRepositoryInterface::class)
			->getMock();
		$eventRepository->method('push')->willReturnCallback(function(Event $event){
			$this->assertEquals(
				'UserUpdated',
				$event->name()
			);

			$this->assertEquals(
				[
					'email' => 'john@domain.com',
					'name' => 'John',
					'isActive' => true,
				],
				$event->data()
			);
		});

		$userAdder = new UserUpdater(
			$userRepository,
			$eventRepository
		);

		$userAdder->update(
			new User(
				new Email('john@domain.com'),
				'John',
				true
			)
		);
	}
}
