<?php

namespace Api\Validators;

use Api\ErrorsList;
use Validator\IsEmailValidator;

class UsersDataValidator
{
	/**
	 * @var int
	 */
	private $nameMinLength;

	/**
	 * @var int
	 */
	private $nameMaxLength;

	/**
	 * @param int $nameMinLength
	 * @param int $nameMaxLength
	 */
	public function __construct(int $nameMinLength, int $nameMaxLength)
	{
		$this->nameMinLength = $nameMinLength;
		$this->nameMaxLength = $nameMaxLength;
	}

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

		if (array_key_exists('name', $json) === false){
			$errors['name'] = [ ErrorsList::NAME_IS_REQUIRED ];
		} else if (strlen($json['name']) < $this->nameMinLength) {
			$errors['name'] = [ ErrorsList::NAME_IS_TOO_SHORT ];
		} else if ($this->nameMaxLength < strlen($json['name'])) {
			$errors['name'] = [ ErrorsList::NAME_IS_TOO_LONG ];
		}

		if (array_key_exists('isActive', $json) === false){
			$errors['isActive'] = [ ErrorsList::IS_ACTIVE_IS_REQUIRED ];
		} else if (is_bool($json['isActive']) === false) {
			$errors['isActive'] = [ ErrorsList::IS_ACTIVE_HAS_TO_BE_BOOLEAN ];
		}

		return $errors;
	}
}
