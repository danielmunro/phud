<?php
namespace Commands;
use \Mechanics\Actor;
	
class Sleep extends Change_Disposition
{
	protected $alias = 'sleep';

	public function perform(Actor $actor, $args = [])
	{
		$this->changeDisposition($actor, $args, 'sleeping');
	}

}
?>
