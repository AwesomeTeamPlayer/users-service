<?php

namespace Api;

use Adapters\Exceptions\UserDoesNotExistException;
use Api\Exceptions\InvalidErrorCodeException;
use Api\Validators\EmailAddressValidator;
use Api\Validators\UsersDataValidator;
use Application\UserUpdater;
use Domain\UsersRepositoryInterface;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\User;
use Psr\Http\Message\RequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Validator\IsEmailValidator;

class GetUserEndpoint extends AbstractEndpoint
{
	/**
	 * @var EmailAddressValidator
	 */
	private $emailAddressValidator;

	/**
	 * @var UserUpdater
	 */
	private $usersRepository;

	/**
	 * @param EmailAddressValidator $emailAddressValidator
	 * @param UsersRepositoryInterface $usersRepository
	 * @param ErrorsListToTextualConverter $errorsListToTextualConverter
	 */
	public function __construct(
		EmailAddressValidator $emailAddressValidator,
		UsersRepositoryInterface $usersRepository,
		ErrorsListToTextualConverter $errorsListToTextualConverter
	)
	{
		parent::__construct($errorsListToTextualConverter);
		$this->emailAddressValidator = $emailAddressValidator;
		$this->usersRepository = $usersRepository;
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 *
	 * @return Response
	 *
	 * @throws InvalidErrorCodeException
	 */
	public function run(Request $request, Response $response) : Response
	{
		$errors = $this->emailAddressValidator->isValid($request);
		if (empty($errors) === false) {
			return $this->getFailedResponse(
				$response,
				$errors
			);
		}

		$email = $this->getEmail($request);

		try {
			$user = $this->usersRepository->get(new Email($email));
		}
		catch (UserDoesNotExistException $exception)
		{
			return $this->getFailedResponse(
				$response,
				[ 'email' => [ ErrorsList::EMAIL_DOES_NOT_EXIST ] ]
			);
		}

		return $response->withJson([
			'status' => 'success',
			'name' => $user->getName(),
			'email' => $user->getEmail()->getEmailAddress(),
			'isActive' => $user->isActive(),
		]);
	}

	/**
	 * @param RequestInterface $request
	 * @return string | null
	 */
	private function getEmail(RequestInterface $request)
	{
		$query = explode('&', $request->getUri()->getQuery());
		foreach ($query as $queryPair) {
			$parts = explode('=', $queryPair);
			if ($parts[0] === 'email') {
				if ($parts[1] === '') {
					return null;
				}

				return urldecode($parts[1]);
			}
		}

		return null;
	}
}
