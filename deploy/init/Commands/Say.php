<?php
namespace Phud\Commands;
use Phud\Actors\Actor;

class Say extends Command
{
	protected $alias = 'say';

	public function perform(Actor $actor, $args = [])
	{
		
		$actors = $actor->getRoom()->getActors();
		
		if(is_array($args))
		{
			array_shift($args);
			$message = implode(' ', $args);
		}
		else if(is_string($args))
			$message = $args;
		
		foreach($actors as $a)
			if($a->getAlias() == $actor->getAlias())
				$a->notify("You say, \"" . $message ."\"");
			else
				$a->notify(ucfirst($actor) . " says, \"" . $message . "\"");
	}
}
