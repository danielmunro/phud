<?php
use \Mechanics\Room;
use \Living\Shopkeeper;
use \Items\Food;

new Room([
	'id' => 1,
	'title' => 'Temple of Midgaard',
	'description' => 'A large temple surrounds you, with sculptures of gods adorning the walls.',
	'area' => 'midgaard',
	'south' => 2
]);

new Room([
	'id' => 2,
	'title' => 'Temple Garden',
	'description' => 'A small fountain lies to the side of the cobblestone path, which dissects an impressive garden. The temple of Midgaard is to the north, with the market square to the south.',
	'area' => 'midgaard',
	'north' => 1,
	'south' => 3
]);

new Room([
	'id' => 3,
	'title' => 'Midgaard Market Square',
	'description' => 'Cobblestone paths from all directions converge here, with shops lining a massive square.',
	'area' => 'midgaard',
	'north' => 2,
	'west' => 4,
	'east' => 7
]);


new Room([
	'id' => 10,
	'title' => 'Blue Moon Bakery',
	'description' => 'You enter a small but brightly lit room. All along the walls are shelves lined with baked goods.',
	'area' => 'midgaard',
	'south' => 4,
	'actors' => [new Shopkeeper([
			'id' => 1,
			'alias' => 'Anyan',
			'nouns' => 'anyan baker',
			'short' => 'Anyan, the town baker',
			'long' => 'An old and wirey elf stands before you.',
			'race' => 'elf',
			'items' => [
				new Food([
					'alias' => 'a baked apple pie',
					'short' => 'a baked apple pie',
					'nouns' => 'baked apple pie',
					'nourishment' => 1,
					'value' => 5
				]),
				new Food([
					'alias' => 'a big pot pie',
					'short' => 'a big pot pie',
					'nouns' => 'pot pie',
					'nourishment' => 2,
					'value' => 8
				])
			]
		])
	]
]);

new Room([
	'id' => 11,
	'title' => 'Midgaard Armory',
	'description' => 'A hallway is lined with implements of destruction on one side, and pieces of various types of armor on the other.',
	'area' => 'midgaard',
	'north' => 4,
	'actors' => [new Shopkeeper([
			'id' => 2,
			'alias' => 'Halek',
			'short' => 'Halek, the blacksmith',
			'nouns' => 'blacksmith halek',
			'race' => 'human',
			'long' => 'A tall and lean blacksmith stands before you.',
			'items' => [
				new Armor([
					'short' => 'brass boots',
					'long' => 'Sturdy brass boots are here.',
					'nouns' => 'brass boots',
					'value' => 50,
					'weight' => 2,
					'level' => 5,
					'material' => Item::MATERIAL_BRASS,
					'attributes' => new Attributes([
						'ac_bash' => 5,
						'ac_slash' => 5,
						'ac_pierce' => 5
					])
				])
			]
		])
	]
]);

$p = [
	'title' => 'Market Street',
	'description' => 'A cobblestone path travels in a east-west direction with shops lining both sides.',
	'area' => 'midgaard'
];

new Room(array_merge(['id' => 4, 'east' => 3, 'west' => 5, 'north' => 10, 'south' => 11], $p));
new Room(array_merge(['id' => 5, 'east' => 4, 'west' => 6], $p));
new Room(array_merge(['id' => 6, 'east' => 5], $p));
new Room(array_merge(['id' => 7, 'east' => 8, 'west' => 3], $p));
new Room(array_merge(['id' => 8, 'east' => 9, 'west' => 7], $p));
new Room(array_merge(['id' => 9, 'west' => 8], $p));

Room::setStartRoom(1);

?>
