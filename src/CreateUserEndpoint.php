<?php

namespace Api;

use Adapters\Exceptions\UserAlreadyExistsException;
use Api\Exceptions\InvalidErrorCodeException;
use Api\Validators\UsersDataValidator;
use Application\UserAdder;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\User;
use Slim\Http\Request;
use Slim\Http\Response;

class CreateUserEndpoint extends AbstractEndpoint
{
	/**
	 * @var UsersDataValidator
	 */
	private $userDataValidator;

	/**
	 * @var UserAdder
	 */
	private $userAdder;

	/**
	 * @param UsersDataValidator $userDataValidator
	 * @param UserAdder $userAdder
	 * @param ErrorsListToTextualConverter $errorsListToTextualConverter
	 */
	public function __construct(
		UsersDataValidator $userDataValidator,
		UserAdder $userAdder,
		ErrorsListToTextualConverter $errorsListToTextualConverter
	)
	{
		parent::__construct($errorsListToTextualConverter);
		$this->userDataValidator = $userDataValidator;
		$this->userAdder = $userAdder;
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
			$this->userAdder->add(
				new User(
					new Email($json['email']),
					$json['name'],
					$json['isActive']
				)
			);
		}
		catch (UserAlreadyExistsException $exception)
		{
			return $this->getFailedResponse(
				$response,
				[ 'login' => [ ErrorsList::EMAIL_EXISTS ] ]
			);
		}

		return $response->withJson([
			'status' => 'success'
		]);
	}

}
