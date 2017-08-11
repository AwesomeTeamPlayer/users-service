<?php

namespace Api\Validators;

use Api\ErrorsList;
use Psr\Http\Message\RequestInterface;
use Validator\IsEmailValidator;

class EmailAddressValidator
{
	/**
	 * @param RequestInterface $request
	 *
	 * @return array
	 */
	public function isValid(RequestInterface $request) : array
	{
		$email = $this->getEmail($request);

		$errors = [];

		$emailValidator = new IsEmailValidator();
		if ($email === null){
			$errors['email'] = [ ErrorsList::EMAIL_IS_REQUIRED ];
		} else if ($emailValidator->valid($email) > 0) {
			$errors['email'] = [ ErrorsList::INCORRECT_EMAIL ];
		}

		return $errors;
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
				return $parts[1];
			}
		}

		return null;
	}
}
