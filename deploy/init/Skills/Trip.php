<?php
	namespace Skills;
    use \Mechanics\Ability\Skill,
		\Mechanics\Affect,
		\Mechanics\Server,
    	\Mechanics\Actor;

	class Trip extends Skill
	{
		protected $alias = 'trip';
		protected $proficiency = 'melee';
		protected $required_proficiency = 20;
		protected $hard_modifier = ['dex'];
		protected $needs_target = true;
		protected $is_offensive = true;
		protected $delay = 1;

		public function getSubscriber()
		{
			return $this->getInputSubscriber();
		}

		protected function applyCost(Actor $actor)
		{
			$cost = 50 - $this->level;
			if($actor->getAttribute('movement') < $cost) {
				Server::out($actor, "You don't have enough energy to trip.");
				return false;
			}
			$actor->modifyAttribute('movement', -($cost));
		}
		
		protected function success(Actor $actor)
		{
			new Affect([
				'affect' => 'stun',
				'timeout' => 1,
				'apply' => $actor->getTarget()
			]);
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => 'You throw a leg out and trip '.$actor->getTarget().'.'],
				['actor' => $actor->getTarget(), 'message' => ucfirst($actor).' trips you and you go down!'],
				['actor' => '*', 'message' => ucfirst($actor).' trips '.$actor->getTarget().' and they go down!']
			]);
		}

		protected function fail(Actor $actor)
		{
			$actor->getRoom()->announce2([
				['actor' => $actor, 'message' => 'You try to trip '.$actor->getTarget().' but fail.'],
				['actor' => $actor->getTarget(), 'message' => ucfirst($actor).' tries to trip you but you evade their attack.'],
				['actor' => '*', 'message' => ucfirst($actor).' tries to trip '.$actor->getTarget().' but they fail.']
			]);
		}
	}
?>
