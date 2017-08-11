<?php

namespace Api\Validators;

use Api\ErrorsList;
use PHPUnit\Framework\TestCase;

class UsersDataValidatorTest extends TestCase
{
	/**
	 * @dataProvider dataProvider
	 */
	public function test_validate($body, $expectedErrors)
	{
		$validator = new UsersDataValidator(3, 255);
		$errors = $validator->validate($body);
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
					'name' => [ ErrorsList::NAME_IS_REQUIRED ],
					'isActive' => [ ErrorsList::IS_ACTIVE_IS_REQUIRED ],
				]
			],
			[
				'{"email":"aaa"}',
				[
					'email' => [ ErrorsList::INCORRECT_EMAIL ],
					'name' => [ ErrorsList::NAME_IS_REQUIRED ],
					'isActive' => [ ErrorsList::IS_ACTIVE_IS_REQUIRED ],
				]
			],
			[
				'{"email":"john@domain.com"}',
				[
					'name' => [ ErrorsList::NAME_IS_REQUIRED ],
					'isActive' => [ ErrorsList::IS_ACTIVE_IS_REQUIRED ],
				]
			],
			[
				'{"email":"john@domain.com", "name":"a"}',
				[
					'name' => [ ErrorsList::NAME_IS_TOO_SHORT ],
					'isActive' => [ ErrorsList::IS_ACTIVE_IS_REQUIRED ],
				]
			],
			[
				'{"email":"john@domain.com", "name":"' . $this->generateString(300) . '"}',
				[
					'name' => [ ErrorsList::NAME_IS_TOO_LONG ],
					'isActive' => [ ErrorsList::IS_ACTIVE_IS_REQUIRED ],
				]
			],
			[
				'{"email":"john@domain.com", "name":"John"}',
				[
					'isActive' => [ ErrorsList::IS_ACTIVE_IS_REQUIRED ],
				]
			],
			[
				'{"email":"john@domain.com", "name":"John", "isActive": "aaa"}',
				[
					'isActive' => [ ErrorsList::IS_ACTIVE_HAS_TO_BE_BOOLEAN ],
				]
			],
			[
				'{"email":"john@domain.com", "name":"John", "isActive":1}',
				[
					'isActive' => [ ErrorsList::IS_ACTIVE_HAS_TO_BE_BOOLEAN ],
				]
			],
			[
				'{"email":"john@domain.com", "name":"John", "isActive":true}',
				[]
			],
		];
	}

	private function generateString($length): string
	{
		$string = '';
		for ($i = 0; $i < $length; $i++) {
			$string .= 'a';
		}

		return $string;
	}
}
