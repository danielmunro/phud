<?php
use Phud\Actors\User,
	Phud\Actors\Actor,
	Phud\Dbr,
	Phud\Server,
	Phud\Races\Race,
	Phud\Room\Room,
	Phud\Debug,
	Phud\Commands\Command;

$server->on('connect', function($event, $server, $client) {
	$client->write("By what name do you wish to be known? ");
	$progress = ['alias' => false];
	$unverified_user = null;
	$user_properties = [];
	$client->on('input', function($event, $client, $input) use ($server, &$progress, &$unverified_user, &$user_properties) {
		$event->satisfy();
		$racesAvailable = function($client) {
			$races = Race::getAliases();
			$client->write("The following races are available: \r\n");
			$race_list = '[';
			foreach($races as $alias => $r) {
				$race_list .= $r['lookup']->isPlayable() ? $alias.' ' : '';
			}
			$client->write(trim($race_list)."]\r\n");
			$client->write("What is your race (help for more information)? ");
		};

		if($progress['alias'] === false) {
			if(!User::validateAlias($input)) {
				return $client->write("That is not a valid name. What IS your name?\r\n");
			}
			$progress['alias'] = $input;
			$unverified_user = unserialize(Dbr::instance()->get($input));
			if(!empty($unverified_user)) {
				$client->write("Password: ");
				$progress['pass'] = false;
			} else {
				$client->write("Did I get that right, ".$progress['alias']."? (y/n) ");
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
				Command::create('look')->perform($unverified_user);
			} else {
				$client->write("Wrong password.");
				$client->fire('quit');
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
					$client->write("New character.\r\n");
					$client->write("Give me a password for ".$user_properties['alias'].": ");
					break;
				case 'n':
				case 'no':
					$progress = ['alias' => false];
					$client->write("Ok, what IS it then? ");
					break;
				default:
					$client->write("Please type Yes or No: ");
			}
			return;
		}
		
		if(isset($progress['new_pass']) && $progress['new_pass'] === false) {
			$progress['new_pass'] = $input;
			$progress['new_pass_2'] = false;
			return $client->write("Please retype password: ");
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
				$client->write("Passwords don't match.\r\nRetype password: ");
			}
			return;
		}
		
		if(isset($progress['race']) && $progress['race'] === false) {
			$progress['race'] = $input;
			$race = Race::record($progress['race']);
			if($race && $race['lookup']->isPlayable()) {
				$user_properties['race'] = $race['alias'];
			} else {
				$client->write("That's not a valid race.\r\n");
				$racesAvailable($client);
				$progress['race'] = false;
				return;
			}
			$client->write("What is your sex (m/f)? ");
			$progress['sex'] = '';
			return;
		}
		
		if(isset($progress['sex']) && $progress['sex'] === '') {
			if($input == 'm' || $input == 'male')
				$progress['sex'] = Actor::SEX_MALE;
			else if($input == 'f' || $input == 'female')
				$progress['sex'] = Actor::SEX_FEMALE;
			else
				return $client->write("That's not a sex.\nWhat IS your sex? ");
			
			if($progress['sex']) {
				$user_properties['sex'] = $progress['sex'];
				$progress['align'] = false;
				return $client->write("What is your alignment (good/neutral/evil)? ");
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
				return $client->write("That's not a valid alignment.\nWhich alignment (g/n/e)? ");
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
			Command::create('look')->perform($user);
			Debug::log("New user account for ".$user);
			$event->kill();
		}
	});
});
?>
