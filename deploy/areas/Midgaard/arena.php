<?php
use \Mechanics\Room,
	\Mechanics\Actor,
	\Living\Mob;

$p = [
	'title' => 'Temple Arena',
	'description' => 'A dirt arena is here, stained with the blood of vanquished foes.',
	'area' => 'temple_arena'
];
$id = 'midgaard_arena';

$snail = [
	'alias' => 'a snail',
	'long' => 'A little snail is trying to get out of the way!',
	'level' => 5,
	'sex' => Actor::SEX_NEUTRAL,
	'race' => 'critter',
	'movement_pulses_timeout' => 50,
	'area' => 'temple_arena',
	'nouns' => 'snail',
	'attributes' => [
		'hp' => 5
	]
];

$lizard = [
	'alias' => 'a lizard',
	'long' => 'A little green lizard slithers across your path.',
	'level' => 6,
	'sex' => Actor::SEX_NEUTRAL,
	'race' => 'critter',
	'movement_pulses_timeout' => 120,
	'area' => 'temple_arena',
	'nouns' => 'lizard',
	'attributes' => [
		'hp' => 8,
		'ac_slash' => 95,
		'ac_bash' => 95,
		'ac_pierce' => 95,
		'ac_magic' => 95
	]
];

$fox = [
	'alias' => 'a fox',
	'long' => 'A small brown fox sneaks across path.',
	'level' => 7,
	'sex' => Actor::SEX_NEUTRAL,
	'race' => 'critter',
	'movement_pulses_timeout' => 120,
	'area' => 'temple_arena',
	'nouns' => 'small brown fox',
	'attributes' => [
		'hp' => 10,
		'ac_slash' => 90,
		'ac_bash' => 90,
		'ac_pierce' => 90,
		'ac_magic' => 90
	]
];

$bird = [
	'alias' => 'a small gray bird',
	'long' => 'A small gray bird tweets as it flies over your shoulder.',
	'level' => 3,
	'sex' => Actor::SEX_NEUTRAL,
	'race' => 'critter',
	'movement_pulses_timeout' => 20,
	'area' => 'temple_arena',
	'nouns' => 'small gray bird',
	'attributes' => [
		'hp' => 4,
		'ac_slash' => 85,
		'ac_bash' => 85,
		'ac_pierce' => 85,
		'ac_magic' => 85
	]
];

// Row 1
new Room(array_merge($p, [
	'id' => $id.'1,1',
	'east' => $id.'2,1',
	'south' => $id.'1,2'
]));

new Room(array_merge($p, [
	'id' => $id.'2,1',
	'east' => $id.'3,1',
	'west' => $id.'1,1',
	'south' => $id.'2,2',
	'actors' => [
		new Mob($lizard)
	]
]));

new Room(array_merge($p, [
	'id' => $id.'3,1',
	'east' => $id.'4,1',
	'west' => $id.'2,1',
	'south' => $id.'3,2',
	'actors' => [
		new Mob($fox)
	]
]));

new Room(array_merge($p, [
	'id' => $id.'4,1',
	'east' => $id.'5,1',
	'west' => $id.'3,1',
	'south' => $id.'4,2',
	'actors' => [
		new Mob($bird),
		new Mob($bird),
		new Mob($bird)
	]
]));

new Room(array_merge($p, [
	'id' => $id.'5,1',
	'west' => $id.'4,1',
	'south' => $id.'5,2',
	'actors' => [
		new Mob($snail),
		new Mob($lizard)
	]
]));

// Row 2
new Room(array_merge($p, [
	'id' => $id.'1,2',
	'east' => $id.'2,2',
	'north' => $id.'1,1',
	'south' => $id.'1,3',
	'actors' => [
		new Mob($lizard)
	]
]));

new Room(array_merge($p, [
	'id' => $id.'2,2',
	'east' => $id.'3,2',
	'west' => $id.'1,2',
	'north' => $id.'2,1',
	'south' => $id.'2,3',
	'actors' => [
		new Mob($snail),
		new Mob($bird),
		new Mob($bird),
		new Mob($bird)
	]
]));

new Room(array_merge($p, [
	'id' => $id.'3,2',
	'east' => $id.'4,2',
	'west' => $id.'2,2',
	'north' => $id.'3,1',
	'south' => $id.'3,3'
]));

new Room(array_merge($p, [
	'id' => $id.'4,2',
	'east' => $id.'5,2',
	'west' => $id.'3,2',
	'north' => $id.'4,1',
	'south' => $id.'4,3',
	'actors' => [
		new Mob($snail),
		new Mob($lizard)
	]
]));

new Room(array_merge($p, [
	'id' => $id.'5,2',
	'west' => $id.'4,2',
	'north' => $id.'5,1',
	'south' => $id.'5,3'
]));

// Row 3
new Room(array_merge($p, [
	'id' => $id.'1,3',
	'east' => $id.'2,3',
	'north' => $id.'1,2',
	'south' => $id.'1,4'
]));

new Room(array_merge($p, [
	'id' => $id.'2,3',
	'east' => $id.'3,3',
	'west' => $id.'1,3',
	'north' => $id.'2,2',
	'south' => $id.'2,4',
	'actors' => [
		new Mob($snail),
		new Mob($fox)
	]
]));

new Room(array_merge($p, [
	'id' => $id.'3,3',
	'east' => $id.'4,3',
	'west' => $id.'2,3',
	'north' => $id.'3,2',
	'south' => $id.'3,4'
]));

new Room(array_merge($p, [
	'id' => $id.'4,3',
	'east' => $id.'5,3',
	'west' => $id.'3,3',
	'north' => $id.'4,2',
	'south' => $id.'4,4',
	'actors' => [
		new Mob($lizard)
	]
]));

new Room(array_merge($p, [
	'id' => $id.'5,3',
	'east' => 2,
	'west' => $id.'4,3',
	'north' => $id.'5,2',
	'south' => $id.'5,4',
	'actors' => [
		new Mob($snail),
		new Mob($fox)
	]
]));

// Row 4
new Room(array_merge($p, [
	'id' => $id.'1,4',
	'east' => $id.'2,4',
	'north' => $id.'1,3',
	'south' => $id.'1,5'
]));

new Room(array_merge($p, [
	'id' => $id.'2,4',
	'east' => $id.'3,4',
	'west' => $id.'1,4',
	'north' => $id.'2,3',
	'south' => $id.'2,5'
]));

new Room(array_merge($p, [
	'id' => $id.'3,4',
	'east' => $id.'4,4',
	'west' => $id.'2,4',
	'north' => $id.'3,3',
	'south' => $id.'3,5',
	'actors' => [
		new Mob($snail)
	]
]));

new Room(array_merge($p, [
	'id' => $id.'4,4',
	'east' => $id.'5,4',
	'west' => $id.'3,4',
	'north' => $id.'4,3',
	'south' => $id.'4,5'
]));

new Room(array_merge($p, [
	'id' => $id.'5,4',
	'west' => $id.'4,4',
	'north' => $id.'5,3',
	'south' => $id.'5,5',
	'actors' => [
		new Mob($lizard),
		new Mob($bird),
		new Mob($bird),
		new Mob($bird)
	]
]));

// Row 5
new Room(array_merge($p, [
	'id' => $id.'1,5',
	'east' => $id.'2,5',
	'north' => $id.'1,4',
	'actors' => [
		new Mob($snail),
		new Mob($fox)
	]
]));

new Room(array_merge($p, [
	'id' => $id.'2,5',
	'east' => $id.'3,5',
	'west' => $id.'1,5',
	'north' => $id.'2,4',
	'actors' => [
		new Mob($snail)
	]
]));

new Room(array_merge($p, [
	'id' => $id.'3,5',
	'east' => $id.'4,5',
	'west' => $id.'2,5',
	'north' => $id.'3,4'
]));

new Room(array_merge($p, [
	'id' => $id.'4,5',
	'east' => $id.'5,5',
	'west' => $id.'3,5',
	'north' => $id.'4,4'
]));

new Room(array_merge($p, [
	'id' => $id.'5,5',
	'west' => $id.'4,5',
	'north' => $id.'5,4',
	'actors' => [
		new Mob($lizard)
	]
]));

?>
