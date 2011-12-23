<?php

	namespace Mechanics;
	class Battle
	{
		protected $actors = array();
		
		public function __construct(Actor $initiator)
		{
			$this->addActor($initiator);
			Server::instance()->addSubscriber(
				new Subscriber(
					Event::EVENT_PULSE,
					$this,
					function($subscriber, $server, $battle) {
						$participant_count = $battle->attackRound();
						if($participant_count === 0) {
							$subscriber->kill();
						}
					}
				)
			);
		}
		
		public function addActor(Actor $actor)
		{
			if(array_search($actor, $this->actors) === false) {
				$this->actors[] = $actor;
			}
		}
		
		public function attackRound()
		{
			foreach($this->actors as $i => $aggressor) {
				$victim = $aggressor->getTarget();
				if($victim) {
					if(!$victim->getTarget()) {
						$victim->setTarget($aggressor);
						$this->addActor($victim);
					}
					$aggressor->fire(Event::EVENT_ATTACK);
				}
				else {
					unset($this->actors[$i]);
				}
			}
			$this->actors = array_values($this->actors);
			return sizeof($this->actors);
		}
	}
?>
