<?php
namespace Phud\Quests;
use Phud\Actors\User;

class Adelwine extends Quest
{
	protected $id = 'adelwine';
	protected $short = 'explore Adelwine manor';
	protected $long = 'There are reports of ghosts in Adelwine manor. Investigate this claim and report back to the acolyte.';
	
	public function getListeners()
	{
		$quest = $this;
		return [
			['moved',
			function($event, $actor, $movement_cost, $room) use ($quest) {
				if($actor->getRoom()->getID() === 40) {
					$quest->setStatus(Quest::STATUS_COMPLETED);
					$event->kill();
				}
			}]
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
