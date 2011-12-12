<?php

	namespace Mechanics;
	class Battle
	{
	
		const SECONDS_PER_BATTLE_ROUND = 1;
		
		protected $actors = array();
		
		public function __construct(Actor $initiator)
		{
			$this->addActor($initiator);
			$this->registerAttackRound();
		}
		
		public function addActor(Actor $actor)
		{
			if(array_search($actor, $this->actors) === false) {
				$this->actors[] = $actor;
			}
		}
		
		public function getActors()
		{
			return $this->actors;
		}
		
		public function removeActor(Actor $actor)
		{
			$key = array_search($actor, $this->actors);
			if($key)
				unset($this->actors[$key]);
			$this->actors = array_values($this->actors);
			array_walk(
					$this->actors,
					function($i) use ($actor)
					{
						if($i->getTarget() === $actor)
							$i->setTarget(null);
					}
				);
			$actor->setTarget(null);
		}
		
		public function registerAttackRound()
		{
			$fn = function($battle)
			{
				$aggressors = $battle->getActors();
				foreach($aggressors as $i => $aggressor)
				{
					$victim = $aggressor->getTarget();
					if($victim)
					{
						if(!$victim->getTarget())
						{
							$victim->setTarget($aggressor);
							$battle->addActor($victim);
						}
						// (Reg)
						$aggressor->attack();
						// (2nd, 3rd, Hst)
                        $aggressor->getAbilitySet()->applySkillsByHook(Ability::HOOK_HIT_ATTACK_ROUND, $victim);
						$aggressor->decrementDelay();
					}
					else
					{
						$battle->removeActor($aggressor);
					}
				}
				
				$aggressors = $battle->getActors();
				foreach($aggressors as $actor)
					if($target = $actor->getTarget())
						Server::out($actor, $target->getAlias(true) . ' ' . $target->getStatus() . ".\n\n" . ($actor instanceof \Living\User ? $actor->prompt() : ''), false);
				
				$battle->registerAttackRound();
			};
			if(sizeof($this->actors))
				Pulse::instance()->registerEvent(self::SECONDS_PER_BATTLE_ROUND, $fn, $this);
		}
	}
?>
