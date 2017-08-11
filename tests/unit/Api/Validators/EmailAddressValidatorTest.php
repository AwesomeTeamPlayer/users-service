<?php

namespace Api\Validators;

use Api\ErrorsList;
use PHPUnit\Framework\TestCase;

class EmailAddressValidatorTest extends TestCase
{
	/**
	 * @dataProvider dataProvider
	 */
	public function test_validate($body, $expectedErrors)
	{
		$validator = new EmailAddressValidator();
		$errors = $validator->isValid($body);
		$this->assertEquals(
			$expectedErrors,
			$errors
		);
	}

	public function dataProvider()
	{
		return [
			[
				'',
				[
					'json' => ErrorsList::INCORRECT_JSON
				]
			],
			[
				'abc123',
				[
					'json' => ErrorsList::INCORRECT_JSON
				]
			],
			[
				'{}',
				[
					'email' => [ ErrorsList::EMAIL_IS_REQUIRED ],
				]
			],
			[
				'{"email":"aaa"}',
				[
					'email' => [ ErrorsList::INCORRECT_EMAIL ],
				]
			],
			[
				'{"email":"john@domain.com"}',
				[
				]
			],
		];
	}
}
