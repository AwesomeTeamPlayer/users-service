<?php

namespace Api;

use Api\Exceptions\InvalidErrorCodeException;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractEndpoint
{
	/**
	 * @var ErrorsListToTextualConverter
	 */
	private $errorsListToTextualConverter;

	/**
	 * @param Request $request
	 * @param Response $response
	 *
	 * @return Response
	 *
	 * @throws InvalidErrorCodeException
	 */
	abstract public function run(Request $request, Response $response) : Response;

	/**
	 * @param ErrorsListToTextualConverter $errorsListToTextualConverter
	 */
	public function __construct(ErrorsListToTextualConverter $errorsListToTextualConverter)
	{
		$this->errorsListToTextualConverter = $errorsListToTextualConverter;
	}

	/**
	 * @param Response $response
	 * @param $errors
	 *
	 * @return Response
	 *
	 * @throws InvalidErrorCodeException
	 */
	protected function getFailedResponse(Response $response, $errors) : Response
	{
		$jsonResponse = ['status' => 'failed'];

		foreach ($errors as $label => $errorsList)
		{
			$jsonResponse[$label] = $this->errorsListToTextualConverter->getTextualErrors($errorsList);
		}

		return $response->withJson($jsonResponse);
	}
}
