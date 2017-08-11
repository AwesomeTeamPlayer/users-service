<?php

namespace Application;

use Adapters\Exceptions\UserAlreadyExistsException;
use AwesomeTeamPlayer\Libraries\Adapters\EventsRepositoryInterface;
use AwesomeTeamPlayer\Libraries\Adapters\ValueObjects\Event;
use DateTime;
use Domain\UsersRepositoryInterface;
use Domain\ValueObjects\User;

class UserAdder
{
	/**
	 * @var UsersRepositoryInterface
	 */
	private $userRepository;

	/**
	 * @var EventsRepositoryInterface
	 */
	private $eventRepository;

	/**
	 * @param UsersRepositoryInterface $userRepository
	 * @param EventsRepositoryInterface $eventRepository
	 */
	public function __construct(
		UsersRepositoryInterface $userRepository,
		EventsRepositoryInterface $eventRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->eventRepository = $eventRepository;
	}

	/**
	 * @param User $user
	 *
	 * @throws UserAlreadyExistsException
	 */
	public function add(User $user)
	{
		$this->userRepository->add($user);
		$this->eventRepository->push(new Event(
			'UserCreated',
			new DateTime(),
			[
				'email' => $user->getEmail()->getEmailAddress(),
				'name' => $user->getName(),
				'isActive' => $user->isActive(),
			]
		));
	}
}
