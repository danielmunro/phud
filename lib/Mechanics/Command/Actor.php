<?php
namespace Mechanics\Command;
use \Mechanics\Actor as mActor;

abstract class Actor extends Command
{
	abstract public function perform(mActor $actor, $args = array());
}
?>
