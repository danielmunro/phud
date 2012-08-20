<?php
namespace Phud\Commands;
use Phud\Actors\User as aUser;

class AttSet extends DM
{
	protected $alias = 'attset';
	protected $min_argument_count = 3;
	
	public function perform(aUser $user, $primary, $attribute, $amount)
	{
		if($primary->setAttribute($attribute, $amount)) {
			$user->notify("You set ".$primary."'s ".$attribute." to ".$amount.".");
			if(method_exists($primary, 'save')) {
				$primary->save();
			}
		} else {
			$user->notify("They don't have that attribute.");
		}
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [
			(new Arguments\Primary())->parse($actor, $args[1]),
			(new Arguments\Attribute())->parse($actor, $args[2]),
			(new Arguments\Number())->parse($actor, $args[3])
		];
	}
}
