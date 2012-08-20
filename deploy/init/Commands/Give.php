<?php
namespace Phud\Commands;
use Phud\Actors\Actor;

class Give extends Command
{
	protected $alias = 'give';
	protected $min_argument_count = 2;
	protected $min_argument_fail = "Give what? To whom?";
	protected $dispositions = [
		Actor::DISPOSITION_STANDING,
		Actor::DISPOSITION_SITTING
	];

	public function perform(Actor $actor, $args = [])
	{
		if(sizeof($args) === 3) {
			$arg_target = $args[2];
			$arg_item = $args[1];
			$amount = 1;
		} else if(sizeof($args) === 4) {
			$arg_target = $args[3];
			$arg_item = $args[2];
			$amount = abs($args[1]);
		} else {
			return $actor->notify("What did you want to give?");
		}

		// Item quantity not figured out yet
		$target = $actor->getRoom()->getActorByInput($arg_target);
		$item = $actor->getItemByInput($arg_item, $amount);

		if(!$target) {
			return $actor->notify("They are not there.");
		}
		
		if($item) {
			$actor->removeItem($item);
			$target->addItem($item);
			$actor->getRoom()->announce2([
				['actor' => $actor,
				'message' => "You give ".$item." to ".$target."."],
				['actor' => $target,
				'message' => ucfirst($actor)." gives ".$item." to you."],
				['actor' => '*',
				'message' => ucfirst($actor)." gives ".$item." to ".$target."."]
			]);
			return;
		}
		
		$currency = '';
		if(strpos('gold', $args[2]) === 0) {
			$currency = 'gold';
		}
		else if(strpos('silver', $args[2]) === 0) {
			$currency = 'silver';
		}
		else if(strpos('copper', $args[2]) === 0) {
			$currency = 'copper';
		}

		if($currency) {
			if($amount > $actor->getCurrency($currency)) {
				return $actor->notify("You do not have enough ".$currency.".");
			}
			$actor->modifyCurrency(-($currency), $amount);
			$target->modifyCurrency($currency, $amount);
			$actor->getRoom()->announce2([
				['actor' => $actor,
				'message' => "You give ".$amount." ".$currency." to ".$target."."],
				['actor' => $target,
				'message' => ucfirst($actor)." gives ".$amount." ".$currency." to you."],
				['actor' => '*',
				'message' => ucfirst($actor)." gives ".$amount." ".$currency." to ".$target."."]
			]);
			return;
		}

		return $actor->notify("What are you trying to give?");
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [$args];
	}
}
