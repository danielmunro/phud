<?php
namespace Mechanics;

/**
 * This class is used by the deploy routine in order to include 
 * the various area and init scripts without local variables in 
 * those scripts colliding.
 */

class Anonymous
{
	public function _require_once($file)
	{
		require_once($file);
	}
}
?>
