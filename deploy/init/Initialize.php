<?php
use Living\User,
	Mechanics\Actor,
	Mechanics\Dbr,
	Phud\Server,
	Mechanics\Race,
	Mechanics\Room,
	Phud\Debug,
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
			$user_properties = [];
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
					function($subscriber, $client, $args) use (&$progress, &$unverified_user, &$input_subscriber, &$user_properties) {
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
								$unverified_user->getRoom()->actorAdd($unverified_user);
								$command = Command::lookup('look');
								$command['lookup']->perform($unverified_user);
								Debug::log("User logged in: ".$unverified_user);
							} else {
								Server::out($client, 'Wrong password.');
								Server::instance()->disconnectClient($client);
							}
							$subscriber->kill();
							return;
						}

						if(isset($progress['confirm_new']) && $progress['confirm_new'] === false) {
							$progress['confirm_new'] = $input;
							switch($progress['confirm_new']) {
								case 'y':
								case 'yes':
									$user_properties['alias'] = $progress['alias'];
									$progress['new_pass'] = false;
									Server::out($client, "New character.");
									Server::out($client, "Give me a password for ".$user_properties['alias'].": ", false);
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
								$user_properties['password'] = $progress['new_pass'];
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
								$user_properties['race'] = $race['alias'];
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
								$progress['sex'] = Actor::SEX_MALE;
							else if($input == 'f' || $input == 'female')
								$progress['sex'] = Actor::SEX_FEMALE;
							else
								return Server::out($client, "That's not a sex.\nWhat IS your sex? ", false);
							
							if($progress['sex']) {
								$user_properties['sex'] = $progress['sex'];
								$progress['align'] = false;
								return Server::out($client, "What is your alignment (good/neutral/evil)? ", false);
							}
						}
						
						if(isset($progress['align']) && $progress['align'] === false)
						{
							if($input == 'g' || $input == 'good')
								$user_properties['alignment'] = 500;
							else if($input == 'n' || $input == 'neutral')
								$user_properties['alignment'] = 0;
							else if($input == 'e' || $input == 'evil')
								$user_properties['alignment'] = -500;
							else
								return Server::out($client, "That's not a valid alignment.\nWhich alignment (g/n/e)? ", false);
							$progress['finish'] = false;
						}

						if(isset($progress['finish']) && $progress['finish'] === false)
						{
							$user = new User($user_properties);
							$client->addSubscriber($input_subscriber);
							$user->modifyCurrency('copper', 20);
							$user->setPassword(sha1($user.$user->getDateCreated().$user_properties['password']));
							$user->setRoom(Room::find(Room::getStartRoom()));
							$user->setClient($client);
							$client->setUser($user);
							$user->save();
							$command = Command::lookup('look');
							$command['lookup']->perform($user);
							Debug::log("New user account for ".$user);
							$subscriber->kill();
						}
					}
				)
			);
		}
	)
);
?>
