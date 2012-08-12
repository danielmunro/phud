<?php
namespace Phud\Commands;
use Phud\Actors\Actor;
	
class Sleep extends Change_Disposition
{
	protected $alias = 'sleep';

	public function perform(Actor $actor, $args = [])
	{
		$this->changeDisposition($actor, $args, 'sleeping');
	}

}
