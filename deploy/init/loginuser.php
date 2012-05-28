<?php
use Phud\Actors\User,
	Phud\Actors\Actor,
	Phud\Dbr,
	Phud\Server,
	Phud\Races\Race,
	Phud\Room,
	Phud\Debug,
	Phud\Commands\Command;

Server::instance()->on('connect', function($event, $server, $client) {
	Server::out($client, 'By what name do you wish to be known? ', false);
	$progress = ['alias' => false];
	$unverified_user = null;
	$user_properties = [];
	$client->on('input', function($event, $client, $args) use (&$progress, &$unverified_user, &$user_properties) {
		$event->satisfy();
		$racesAvailable = function($client) {
			$races = Race::getAliases();
			Server::out($client, 'The following races are available: ');
			$race_list = '[';
			foreach($races as $alias => $r) {
				$race_list .= $r['lookup']->isPlayable() ? $alias.' ' : '';
			}
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
				$unverified_user->getRoom()->actorAdd($unverified_user);
				Command::lookup('look')->perform($unverified_user);
				Debug::log("User logged in: ".$unverified_user);
			} else {
				Server::out($client, 'Wrong password.');
				Server::instance()->disconnectClient($client);
			}
			$event->kill();
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
			$race = Race::record($progress['race']);
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
		
		if(isset($progress['align']) && $progress['align'] === false) {
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

		if(isset($progress['finish']) && $progress['finish'] === false) {
			$user = new User($user_properties);
			$user->modifyCurrency('copper', 20);
			$user->setPassword(sha1($user.$user->getDateCreated().$user_properties['password']));
			$user->setRoom(Room::getByID(Room::getStartRoom()));
			$user->setClient($client);
			$client->setUser($user);
			$user->save();
			Command::lookup('look')->perform($user);
			Debug::log("New user account for ".$user);
			$event->kill();
		}
	});
});
?>
