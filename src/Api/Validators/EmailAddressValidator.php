<?php

namespace Api\Validators;

use Api\ErrorsList;
use Validator\IsEmailValidator;

class EmailAddressValidator
{
	/**
	 * @param string $requestBody
	 *
	 * @return array
	 */
	public function isValid(string $requestBody) : array
	{
		$json = json_decode($requestBody, true);
		if ($json === null) {
			return [
				'json' => ErrorsList::INCORRECT_JSON,
			];
		}

		$errors = [];

		$emailValidator = new IsEmailValidator();
		if (array_key_exists('email', $json) === false){
			$errors['email'] = [ ErrorsList::EMAIL_IS_REQUIRED ];
		} else if ($emailValidator->valid($json['email']) > 0) {
			$errors['email'] = [ ErrorsList::INCORRECT_EMAIL ];
		}

		return $errors;
	}
}
