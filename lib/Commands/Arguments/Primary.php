<?php
namespace Phud\Commands\Arguments;
use Phud\Actors\Actor as aActor,
	Phud\Items\Item as iItem;

class Primary extends Argument
{
	protected function parseArg(aActor $actor, $arg)
	{
		$seeking = $actor->getRoom()->getActorByInput($arg);
		if(!$seeking) {
			$seeking = $actor->getRoom()->getItemByInput($arg) || $actor->getItemByInput($arg);
			if(!$seeking) {
				$this->status = self::STATUS_INVALID;
				return $actor->notify("Nothing like that exists on heaven or earth.");
			}
		}
		return $seeking;
	}
}
