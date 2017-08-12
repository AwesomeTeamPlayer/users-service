<?php

namespace Api;

use Adapters\MysqlUserRepository;
use Api\Validators\EmailAddressValidator;
use Api\Validators\UsersDataValidator;
use Application\UserAdder;
use Application\UserUpdater;
use AwesomeTeamPlayer\Libraries\Adapters\EventsRepositoryInterface;
use AwesomeTeamPlayer\Libraries\Adapters\RabbitMqEventsRepository;
use mysqli;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Slim\App;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class ApplicationBuilder
{
	/**
	 * @param ApplicationConfig $applicationConfig
	 *
	 * @return App
	 */
	public function build(ApplicationConfig $applicationConfig) : App
	{
		$mysqli = $this->getMysqli($applicationConfig);
		$amqp = $this->getAmqpStreamConnection($applicationConfig);

		$app = new App(new Container(
			[
				'settings' => [
					'displayErrorDetails' => true,
				],
			]
		));

		$usersRepository = new MysqlUserRepository($mysqli);
		$eventsRepository = $this->buildEventRepository($applicationConfig);

		$usersDataValidator = new UsersDataValidator($applicationConfig->getMinNameLength(), $applicationConfig->getMaxNameLength());
		$errorsListToTextualConverter = new ErrorsListToTextualConverter($applicationConfig->getMinNameLength(), $applicationConfig->getMaxNameLength());

		$updateUserEndpoint = new UpdateUserEndpoint(
			$usersDataValidator,
			new UserUpdater($usersRepository, $eventsRepository),
			$errorsListToTextualConverter
		);

		$createUserEndpoint = new CreateUserEndpoint(
			$usersDataValidator,
			new UserAdder($usersRepository, $eventsRepository),
			$errorsListToTextualConverter
		);

		$getUserEndpoint = new GetUserEndpoint(
			new EmailAddressValidator(),
			$usersRepository,
			$errorsListToTextualConverter
		);

		$app->post('/users', function (Request $request, Response $response) use ($updateUserEndpoint) {
			return $updateUserEndpoint->run($request, $response);
		});

		$app->put('/users', function (Request $request, Response $response) use ($createUserEndpoint) {
			return $createUserEndpoint->run($request, $response);
		});

		$app->get('/users', function (Request $request, Response $response) use ($getUserEndpoint) {
			return $getUserEndpoint->run($request, $response);
		});

		$app->get('/', function (Request $request, Response $response) use ($applicationConfig, $mysqli, $amqp) {
			return $response->withJson(
				[
					'type' => 'users-service',
					'config' => $applicationConfig->getArray(),
					'status' => [
						'is_connected'=> [
							'MySQL' => $mysqli->ping(),
							'RabbitMQ' => $amqp->isConnected(),
						],
					],
				]
			);
		});

		return $app;
	}

	/**
	 * @param ApplicationConfig $applicationConfig
	 *
	 * @return mysqli
	 */
	private function getMysqli(ApplicationConfig $applicationConfig) : mysqli
	{
		return new mysqli(
			$applicationConfig->getMysqlHost(),
			$applicationConfig->getMysqlUser(),
			$applicationConfig->getMysqlPassword(),
			$applicationConfig->getMysqlDatabase(),
			$applicationConfig->getMysqlPort()
		);
	}

	/**
	 * @param ApplicationConfig $applicationConfig
	 *
	 * @return EventsRepositoryInterface
	 */
	private function buildEventRepository(ApplicationConfig $applicationConfig) : EventsRepositoryInterface
	{
		$connection = $this->getAmqpStreamConnection($applicationConfig);
		$channel = $connection->channel();
		$channel->queue_declare(
			$applicationConfig->getRabbitmqChannel(),
			false,
			false,
			false,
			false
		);

		return new RabbitMqEventsRepository(
			$channel,
			$applicationConfig->getRabbitmqChannel()
		);
	}

	/**
	 * @param ApplicationConfig $applicationConfig
	 *
	 * @return AMQPStreamConnection
	 */
	private function getAmqpStreamConnection(ApplicationConfig $applicationConfig) : AMQPStreamConnection
	{
		return new AMQPStreamConnection(
			$applicationConfig->getRabbitmqHost(),
			$applicationConfig->getRabbitmqPort(),
			$applicationConfig->getRabbitmqUser(),
			$applicationConfig->getRabbitmqPassword()
		);
	}
}
