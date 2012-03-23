<?php
use \Living\Questmaster,
	\Mechanics\Quest\Quest,
	\Mechanics\Event\Subscriber,
	\Mechanics\Event\Event;

$adept = Questmaster::getByID(1);

$adept->addQuest(
	new Quest([
		'id' => 1,
		'short' => 'explore Adelwine manor',
		'long' => 'There are reports of ghosts in Adelwine manor. Investigate this claim and report back to the acolyte.',
		'requirements_to_accept' => function($user) {
			return true;
		},
		'reward' => function($user) {
			$user->addExperience(1000);
		},
		'subscribers' => [
			new Subscriber(
				Event::EVENT_MOVED,
				function($subscriber, $user, $movement_cost, $room) {
					if($user->getRoom()->getID() === 40) {
						$quest = $user->getQuestByID(1);
						$quest->setStatus(Quest::STATUS_COMPLETED);
						$subscriber->kill();
					}
				}
			)
		]
	])
);

?>
