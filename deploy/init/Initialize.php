<?php
use Living\User,
	Mechanics\Dbr,
	Mechanics\Server,
	Mechanics\Race,
	Mechanics\Room,
	Mechanics\Debug,
	Mechanics\Command\Command,
	Mechanics\Event\Subscriber,
	Mechanics\Event\Event;

$server = Server::instance();
$server->addSubscriber(
	new Subscriber(
		Event::EVENT_CONNECTED,
		function($subscriber, $server, $client) {
			Server::out($client, 'By what name do you wish to be known? ', false);
			$progress = ['alias' => false];
			$unverified_user = null;
			$input_subscriber = new Subscriber(
				Event::EVENT_INPUT,
				function($subscriber, $client, $args) {
					$command = Command::lookup($args[0]);
					if($command) {
						$command['lookup']->tryPerform($client->getUser(), $args, $subscriber);
						$subscriber->satisfyBroadcast();
					}
				}
			);
			$client->addSubscriber(
				new Subscriber(
					Event::EVENT_INPUT,
					function($subscriber, $client, $args) use (&$progress, &$unverified_user, &$input_subscriber) {
						$subscriber->satisfyBroadcast();
						$racesAvailable = function($client) {
							$races = Race::getAliases();
							Server::out($client, 'The following races are available: ');
							$race_list = '[';
							array_walk(
								$races,
								function($r, $k) use (&$race_list) {
									if($r['lookup']->isPlayable()) {
										$race_list .= $k.' ';
									}
								}
							);
							Server::out($client, trim($race_list).']');
							Server::out($client, 'What is your race (help for more information)? ', false);
						};

						$input = array_shift($args);
						if($progress['alias'] === false) {
							if(!User::validateAlias($input)) {
								return Server::out($client, "That is not a valid name. What IS your name?");
							}
							$progress['alias'] = $input;
							$unverified_user = unserialize(Dbr::instance()->get($input));
							if(!empty($unverified_user)) {
								Server::out($client, 'Password: ', false);
								$progress['pass'] = false;
							} else {
								Server::out($client, 'Did I get that right, ' . $progress['alias'] . '? (y/n) ', false);
								$progress['confirm_new'] = false;
							}
							return;
						}
						
						if(isset($progress['pass']) && $progress['pass'] === false) {
							$progress['pass'] = $input;
							$pw_hash = sha1($unverified_user.$unverified_user->getDateCreated().$progress['pass']);
							if($unverified_user->getPassword() === $pw_hash) {
								$unverified_user->setClient($client);
								$client->setUser($unverified_user);
								$client->addSubscriber($input_subscriber);
								User::addInstance($unverified_user);
								$unverified_user->getRoom()->actorAdd($unverified_user);
								$command = Command::lookup('look');
								$command['lookup']->perform($unverified_user);
							} else {
								Server::out($client, 'Wrong password.');
								$subscriber->kill();
								Server::instance()->disconnectClient($client);
							}
							return;
						}

						if(isset($progress['confirm_new']) && $progress['confirm_new'] === false) {
							$progress['confirm_new'] = $input;
							switch($progress['confirm_new']) {
								case 'y':
								case 'yes':
									$unverified_user = new User();
									$unverified_user->setAlias($progress['alias']);
									$progress['new_pass'] = false;
									Server::out($client, "New character.");
									Server::out($client, "Give me a password for ".$progress['alias'].": ", false);
									break;
								case 'n':
								case 'no':
									$progress = ['alias' => false];
									Server::out($client, "Ok, what IS it then? ", false);
									break;
								default:
									Server::out($client, 'Please type Yes or No: ', false);
							}
							return;
						}
						
						if(isset($progress['new_pass']) && $progress['new_pass'] === false) {
							$progress['new_pass'] = $input;
							$progress['new_pass_2'] = false;
							return Server::out($client, 'Please retype password: ', false);
						}
						
						if(isset($progress['new_pass_2']) && $progress['new_pass_2'] === false) {
							$progress['new_pass_2'] = $input;
							if($progress['new_pass'] == $progress['new_pass_2']) {
								$progress['race'] = false;
								$racesAvailable($client);
							} else {
								unset($progress['new_pass_2']);
								$progress['new_pass'] = false;
								Server::out($client, "Passwords don't match.");
								Server::out($client, "Retype password: ", false);
							}
							return;
						}
						
						if(isset($progress['race']) && $progress['race'] === false) {
							$progress['race'] = $input;
							$race = Race::lookup($progress['race']);
							if($race && $race['lookup']->isPlayable()) {
								$unverified_user->setRace($race);
							} else {
								Server::out($client, "That's not a valid race.");
								$racesAvailable($client);
								$progress['race'] = false;
								return;
							}
							Server::out($client, "What is your sex (m/f)? ", false);
							$progress['sex'] = '';
							return;
						}
						
						if(isset($progress['sex']) && $progress['sex'] === '') {
							if($input == 'm' || $input == 'male')
								$progress['sex'] = 'm';
							else if($input == 'f' || $input == 'female')
								$progress['sex'] = 'f';
							else
								return Server::out($client, "That's not a sex.\nWhat IS your sex? ", false);
							
							if($progress['sex']) {
								$unverified_user->setSex($progress['sex']);
								$progress['align'] = false;
								return Server::out($client, "What is your alignment (good/neutral/evil)? ", false);
							}
						}
						
						if(isset($progress['align']) && $progress['align'] === false)
						{
							if($input == 'g' || $input == 'good')
								$progress['align'] = 500;
							else if($input == 'n' || $input == 'neutral')
								$progress['align'] = 0;
							else if($input == 'e' || $input == 'evil')
								$progress['align'] = -500;
							else
								return Server::out($client, "That's not a valid alignment.\nWhich alignment (g/n/e)? ", false);
							
							$unverified_user->modifyAlignment($progress['align']);
							$progress['finish'] = false;
						}

						if(isset($progress['finish']) && $progress['finish'] === false)
						{
							$client->addSubscriber($input_subscriber);
							User::addInstance($unverified_user);
							$unverified_user->setAlias($progress['alias']);
							$unverified_user->modifyCurrency('copper', 20);
							$unverified_user->setPassword(sha1($unverified_user.$unverified_user->getDateCreated().$progress['new_pass']));
							$unverified_user->setRoom(Room::find(Room::getStartRoom()));
							$unverified_user->setClient($client);
							$unverified_user->save();
							
							$command = Command::lookup('look');
							$command['lookup']->perform($unverified_user);
							Server::out($client, $unverified_user->prompt(), false);
						}
						Debug::log($unverified_user." logged in");
						$subscriber->kill();
					}
				)
			);
		}
	)
);
?>
