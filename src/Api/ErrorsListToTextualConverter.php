<?php

namespace Api;

use Api\Exceptions\InvalidErrorCodeException;

class ErrorsListToTextualConverter
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
	 * @param int[] $listOfErrorsCode
	 *
	 * @return string[]
	 *
	 * @throws InvalidErrorCodeException
	 */
	public function getTextualErrors(array $listOfErrorsCode) : array
	{
		$listOfTextualErrors = [];

		foreach ($listOfErrorsCode as $key => $errorCode) {
			$listOfTextualErrors[] = [
				'codeId' => $errorCode,
				'text' => $this->getTextualError($errorCode),
			];

		}

		return $listOfTextualErrors;
	}

	/**
	 * @param int $errorCode
	 *
	 * @return string
	 *
	 * @throws InvalidErrorCodeException
	 */
	private function getTextualError(int $errorCode) : string
	{
		switch ($errorCode) {
			case ErrorsList::INCORRECT_JSON:
				return 'Json is incorrect';
			case ErrorsList::EMAIL_IS_REQUIRED:
				return 'Email is required';
			case ErrorsList::INCORRECT_EMAIL:
				return 'Given email address is incorrect';
			case ErrorsList::EMAIL_EXISTS:
				return 'Given email address already exists';
			case ErrorsList::EMAIL_DOES_NOT_EXIST:
				return 'Given email address does not exists';
			case ErrorsList::NAME_IS_REQUIRED:
				return 'Name is required';
			case ErrorsList::NAME_IS_TOO_SHORT:
				return 'Name is too short (minimal length is ' . $this->nameMinLength . ')';
			case ErrorsList::NAME_IS_TOO_LONG:
				return 'Name is too long (maximal length is ' . $this->nameMaxLength . ')';
			case ErrorsList::IS_ACTIVE_IS_REQUIRED:
				return 'IsActive value is required';
			case ErrorsList::IS_ACTIVE_HAS_TO_BE_BOOLEAN:
				return 'IsActive value has to be boolean value';
		}

		throw new InvalidErrorCodeException();
	}
}
