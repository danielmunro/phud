<?php
use Mechanics\Server,
	Mechanics\Event\Subscriber,
	Mechanics\Event\Event;

$server = Server::instance();
$server->addSubscriber(
	new Subscriber(
		Event::EVENT_CONNECTED,
		function($subscriber, $server, $client) {
			Server::out($client, 'By what name do you wish to be known? ', false);
		}
	)
);
?>
