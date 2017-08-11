<?php

namespace Application;

use Adapters\Exceptions\UserAlreadyExistsException;
use AwesomeTeamPlayer\Libraries\Adapters\EventsRepositoryInterface;
use AwesomeTeamPlayer\Libraries\Adapters\ValueObjects\Event;
use Domain\UsersRepositoryInterface;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\User;
use PHPUnit\Framework\TestCase;

class UserAdderTest extends TestCase
{
	/**
	 * @expectedException \Adapters\Exceptions\UserAlreadyExistsException
	 */
	public function test_when_user_exists()
	{
		$userRepository = $this->getMockBuilder(UsersRepositoryInterface::class)
			->getMock();
		$userRepository->method('add')->willThrowException(new UserAlreadyExistsException());

		$eventRepository = $this->getMockBuilder(EventsRepositoryInterface::class)
			->getMock();
		$eventRepository->expects($this->never())->method('push');

		$userAdder = new UserAdder(
			$userRepository,
			$eventRepository
		);

		$userAdder->add(
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
				'UserCreated',
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

		$userAdder = new UserAdder(
			$userRepository,
			$eventRepository
		);

		$userAdder->add(
			new User(
				new Email('john@domain.com'),
				'John',
				true
			)
		);
	}
}
