<?php
namespace Mechanics;
class Tag
{
	public static function apply($message, $ansii = null)
	{
		if($ansii === null)
		{
			if(strpos($message, 'questmaster') === 0)
				$ansii = '[37m';
			if(strpos($message, 'Quest Award') === 0)
				$ansii = '[31m';
		}
		return '(' . chr(27) . $ansii . $message . chr(27) . '[0m) ';
	}
}
?>
