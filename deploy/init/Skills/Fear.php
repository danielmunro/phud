<?php
namespace Skills;
use \Mechanics\Ability\Ability,
	\Mechanics\Ability\Skill,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Affect,
	\Mechanics\Race;

class Fear extends Skill
{
	protected $alias = 'fear';
	protected $proficiency = 'maladictions';
	protected $required_proficiency = 25;
	protected $normal_modifier = ['cha'];
	protected $requires_target = true;

	public function getSubscriber()
	{
		return $this->getInputSubscriber();
	}

	protected function applyCost(Actor $actor)
	{
		$cost = 75 - $actor->getLevel();
		if($actor->getAttribute('movement') < $cost) {
			Server::out($actor, "You don't have enough energy to instill fear.");
			return false;
		}
		$actor->incrementDelay(2);
		$actor->modifyAttribute('movement', -($cost));
	}
	
	protected function success(Actor $actor)
	{
		$mod = round(min(3, 3 * ($actor->getLevel()/Actor::MAX_LEVEL)));
		new Affect([
			'affect' => 'fear',
			'timeout' => max(2, round($actor->getProficiencyIn($this->proficiency) / 10)),
			'message_affect' => 'Affect: fear. Decrease strength and constitution by '.$mod,
			'message_end' => 'You are no longer fearful.',
			'attributes' => [
				'str' => $mod,
				'con' => $mod
			],
			'apply' => $actor->getTarget()
		]);
		$actor->getRoom()->announce2([
			['actor' => $actor, 'message' => 'You invoke a sense of fear in '.$actor->getTarget().'!'],
			['actor' => $actor->getTarget(), 'message' => ucfirst($actor).' invokes a sense of fear in you!'],
			['actor' => '*', 'message' => ucfirst($actor).' invokes a sense of fear in '.$actor->getTarget().'!']
		]);
	}

	protected function fail(Actor $actor)
	{
		$actor->getRoom()->announce2([
			['actor' => $actor, 'message' => 'Your fearmongering fails.'],
			['actor' => $actor->getTarget(), 'message' => 'You bravely stand your ground to '.$actor."'s fearmongering!"],
			['actor' => '*', 'message' => ucfirst($actor)."'s fearmongering fails to affect ".$actor->getTarget()."."]
		]);
	}
}
?>
