<?php

namespace Api;

use PHPUnit\Framework\TestCase;

class ErrorsListToTextualConverterTest extends TestCase
{
	/**
	 * @expectedException \Api\Exceptions\InvalidErrorCodeException
	 */
	public function test_getTextualErrors_with_incorrect_error_code()
	{
		$converter = new ErrorsListToTextualConverter(3, 255);
		$response = $converter->getTextualErrors([
			-1
		]);
	}

	/**
	 * @dataProvider dataProvider
	 */
	public function test_getTextualErrors($listOfErrorCodes, $expectedResponse)
	{
		$converter = new ErrorsListToTextualConverter(3, 255);
		$response = $converter->getTextualErrors($listOfErrorCodes);
		$this->assertEquals($expectedResponse, $response);
	}

	public function dataProvider()
	{
		return [
			[
				[],
				[]
			],
			[
				[ ErrorsList::INCORRECT_JSON ],
				[
					[
						'codeId' => ErrorsList::INCORRECT_JSON,
						'text' => 'Json is incorrect',
					]
				]
			],
			[
				[ ErrorsList::EMAIL_IS_REQUIRED ],
				[
					[
						'codeId' => ErrorsList::EMAIL_IS_REQUIRED,
						'text' => 'Email is required',
					]
				]
			],
			[
				[ ErrorsList::INCORRECT_EMAIL ],
				[
					[
						'codeId' => ErrorsList::INCORRECT_EMAIL,
						'text' => 'Given email address is incorrect',
					]
				]
			],
			[
				[ ErrorsList::EMAIL_EXISTS ],
				[
					[
						'codeId' => ErrorsList::EMAIL_EXISTS,
						'text' => 'Given email address already exists',
					]
				]
			],
			[
				[ ErrorsList::EMAIL_DOES_NOT_EXIST ],
				[
					[
						'codeId' => ErrorsList::EMAIL_DOES_NOT_EXIST,
						'text' => 'Given email address does not exists',
					]
				]
			],
			[
				[ ErrorsList::NAME_IS_REQUIRED ],
				[
					[
						'codeId' => ErrorsList::NAME_IS_REQUIRED,
						'text' => 'Name is required',
					]
				]
			],
			[
				[ ErrorsList::NAME_IS_TOO_SHORT ],
				[
					[
						'codeId' => ErrorsList::NAME_IS_TOO_SHORT,
						'text' => 'Name is too short (minimal length is 3)',
					]
				]
			],
			[
				[ ErrorsList::NAME_IS_TOO_LONG ],
				[
					[
						'codeId' => ErrorsList::NAME_IS_TOO_LONG,
						'text' => 'Name is too long (maximal length is 255)',
					]
				]
			],
			[
				[ ErrorsList::IS_ACTIVE_IS_REQUIRED ],
				[
					[
						'codeId' => ErrorsList::IS_ACTIVE_IS_REQUIRED,
						'text' => 'IsActive value is required',
					]
				]
			],
			[
				[ ErrorsList::IS_ACTIVE_HAS_TO_BE_BOOLEAN ],
				[
					[
						'codeId' => ErrorsList::IS_ACTIVE_HAS_TO_BE_BOOLEAN,
						'text' => 'IsActive value has to be boolean value',
					]
				]
			],
		];
	}
}
