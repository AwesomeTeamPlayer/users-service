<?php

namespace Api;

use Adapters\Exceptions\UserDoesNotExistException;
use Api\Exceptions\InvalidErrorCodeException;
use Api\Validators\UsersDataValidator;
use Application\UserUpdater;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\User;
use Slim\Http\Request;
use Slim\Http\Response;

class UpdateUserEndpoint extends AbstractEndpoint
{
	/**
	 * @var UsersDataValidator
	 */
	private $userDataValidator;

	/**
	 * @var UserUpdater
	 */
	private $userUpdater;

	/**
	 * @param UsersDataValidator $userDataValidator
	 * @param UserUpdater $userUpdater
	 * @param ErrorsListToTextualConverter $errorsListToTextualConverter
	 */
	public function __construct(
		UsersDataValidator $userDataValidator,
		UserUpdater $userUpdater,
		ErrorsListToTextualConverter $errorsListToTextualConverter
	)
	{
		parent::__construct($errorsListToTextualConverter);
		$this->userDataValidator = $userDataValidator;
		$this->userUpdater = $userUpdater;
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
		$errors = $this->userDataValidator->isValid($request->getBody());
		if (empty($errors) === false) {
			return $this->getFailedResponse(
				$response,
				$errors
			);
		}

		$json = json_decode($request->getBody(), true);

		try {
			$this->userUpdater->update(
				new User(
					new Email($json['email']),
					$json['name'],
					$json['isActive']
				)
			);
		}
		catch (UserDoesNotExistException $exception)
		{
			return $this->getFailedResponse(
				$response,
				[ 'email' => [ ErrorsList::EMAIL_DOES_NOT_EXIST ] ]
			);
		}

		return $response->withJson([
			'status' => 'success'
		]);
	}

}
