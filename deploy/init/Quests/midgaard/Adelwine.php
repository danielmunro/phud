<?php
namespace Phud\Quests;
use Phud\Event\Subscriber,
	Phud\Event\Event,
	Phud\Actors\User;

class Adelwine extends Quest
{
	protected $id = 'adelwine';
	protected $short = 'explore Adelwine manor';
	protected $long = 'There are reports of ghosts in Adelwine manor. Investigate this claim and report back to the acolyte.';
	
	public function getSubscribers()
	{
		$quest = $this;
		return [
			new Subscriber(
				Event::EVENT_MOVED,
				function($subscriber, $user, $movement_cost, $room) use ($quest) {
					if($user->getRoom()->getID() === 40) {
						$quest->setStatus(Quest::STATUS_COMPLETED);
						$subscriber->kill();
					}
				}
			)
		];
	}

	public function canAccept(User $user)
	{
		return true;
	}

	public function reward(User $user)
	{
		$user->addExperience(1000);
	}
}
?>
