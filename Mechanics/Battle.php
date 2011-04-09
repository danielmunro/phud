<?php

	namespace Mechanics;
	class Battle
	{
		protected $actors = array();
		
		public function __construct(Actor $initiator)
		{
			$this->addActor($initiator);
			$this->registerAttackRound();
		}
		
		public function addActor(Actor &$actor)
		{
			$this->actors[] = $actor;
		}
		
		public function getActors()
		{
			return $this->actors;
		}
		
		public function removeActor($i)
		{
			unset($this->actors[$i]);
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
						$aggressor->attack();
					}
					else
					{
						$battle->removeActor($i);
					}
				}
				
				$aggressors = $battle->getActors();
				foreach($aggressors as $actor)
					if($target = $actor->getTarget())
						Server::out($actor, $target->getAlias(true) . ' ' . $target->getStatus() . ".\n\n" . ($actor instanceof \Living\User ? $actor->prompt() : ''), false);
				
				$battle->registerAttackRound();
			};
			if(sizeof($this->actors))
				ActorObserver::instance()->registerPulseEvent(1, $fn, $this);
		}
	}
?>
