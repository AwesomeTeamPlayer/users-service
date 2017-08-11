<?php

namespace Application;

use Adapters\Exceptions\UserDoesNotExistException;
use AwesomeTeamPlayer\Libraries\Adapters\EventsRepositoryInterface;
use AwesomeTeamPlayer\Libraries\Adapters\ValueObjects\Event;
use DateTime;
use Domain\UsersRepositoryInterface;
use Domain\ValueObjects\User;

class UserUpdater
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
	 * @throws UserDoesNotExistException
	 */
	public function update(User $user)
	{
		$this->userRepository->update($user);
		$this->eventRepository->push(new Event(
			'UserUpdated',
			new DateTime(),
			[
				'email' => $user->getEmail()->getEmailAddress(),
				'name' => $user->getName(),
				'isActive' => $user->isActive(),
			]
		));
	}
}
