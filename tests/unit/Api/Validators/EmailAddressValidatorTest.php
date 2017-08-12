<?php

namespace Api\Validators;

use Api\ErrorsList;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class EmailAddressValidatorTest extends TestCase
{
	/**
	 * @dataProvider dataProvider
	 */
	public function test_validate($email, $expectedErrors)
	{
		$request = $this->getMockBuilder(RequestInterface::class)
			->setMethods([
				'getRequestTarget',
				'withRequestTarget',
				'getMethod',
				'withMethod',
				'getUri',
				'withUri',
				'getProtocolVersion',
				'withProtocolVersion',
				'getHeaders',
				'hasHeader',
				'getHeader',
				'getHeaderLine',
				'withHeader',
				'withAddedHeader',
				'withoutHeader',
				'getBody',
				'withBody',
			])
			->getMock();
		$request->method('getUri')->willReturn(
			new Uri(
				'http://domain.com/abc?email=' . $email
			)
		);

		$validator = new EmailAddressValidator();
		$errors = $validator->isValid($request);
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
					'email' => [ ErrorsList::EMAIL_IS_REQUIRED ]
				]
			],
			[
				'aaa',
				[
					'email' => [ ErrorsList::INCORRECT_EMAIL ],
				]
			],
			[
				'john@domain.com',
				[
				]
			],
		];
	}
}
