<?php
namespace Mechanics\Command;
use \Living\User as lUser;

abstract class User extends Command
{
	abstract public function perform(lUser $user, $args = array());
}
?>
