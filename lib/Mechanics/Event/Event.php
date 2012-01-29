<?php
namespace Mechanics\Event;

class Event
{
	const EVENT_MOVED = 'moved';
	const EVENT_INPUT = 'input';
	const EVENT_PULSE = 'pulse';
	const EVENT_TICK = 'tick';
	const EVENT_CONNECTED = 'connected';
	const EVENT_GAME_CYCLE = 'cycle';
	const EVENT_ATTACKED = 'attacked';
	const EVENT_MELEE_ATTACK = 'melee attack';
	const EVENT_MELEE_ATTACKED = 'melee attacked';
	const EVENT_DAMAGE_MODIFIER_ATTACKING = 'single round attack modifier';
	const EVENT_DAMAGE_MODIFIER_DEFENDING = 'single round defense modifier';
	const EVENT_CASTING = 'casting';
	const EVENT_CASTED_AT = 'casted at';
	const EVENT_BUY = 'buy';
	const EVENT_BASHED = 'bashed';
	const EVENT_APPLY_AFFECT = 'apply affect';
}
?>
