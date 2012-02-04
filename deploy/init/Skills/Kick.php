<?php
namespace Skills;
use \Mechanics\Ability\Skill,
	\Mechanics\Server,
	\Mechanics\Actor,
	\Mechanics\Damage;

class Kick extends Skill
{
	protected $alias = 'kick';
	protected $proficiency = 'melee';
	protected $required_proficiency = 20;
	protected $normal_modifier = ['dex', 'str'];
	protected $needs_target = true;
	protected $is_offensive = true;
	protected $delay = 1;

	public function getSubscriber()
	{
		return $this->getInputSubscriber();
	}

	protected function applyCost(Actor $actor)
	{
	}

	protected function fail(Actor $actor)
	{
		$actor->getRoom()->announce2([
			['actor' => $actor, 'message' => "Your kick misses ".$actor->getTarget()." harmlessly."],
			['actor' => $actor->getTarget(), 'message' => ucfirst($actor)."'s kick misses you harmlessly."],
			['actor' => '*', 'message' => ucfirst($actor)."'s kick misses ".$actor->getTarget()." harmlessly."]
		]);
	}

	protected function success(Actor $actor)
	{
		$damage = rand(1, (1+$actor->getLevel()/2));
		$actor->getTarget()->modifyAttribute('hp', -($damage));
		$sexes = [Actor::SEX_MALE => "him", Actor::SEX_FEMALE => "her", Actor::SEX_NEUTRAL => "it"];
		$s = $actor->getDisplaySex($sexes);
		$actor->getRoom()->announce2([
			['actor' => $actor, 'message' => "Your kick hits ".$actor->getTarget().", causing ".$s." pain!"],
			['actor' => $actor->getTarget(), 'message' => ucfirst($actor)."'s kick hits you!"],
			['actor' => '*', 'message' => ucfirst($actor)."'s kick hits ".$actor->getTarget().", causing ".$s." pain!"]
		]);
	}
}

?>
