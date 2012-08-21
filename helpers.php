<?php

function chance() {
	return rand(0, 10000) / 10000;
}

function _range($min, $max, $n) {
	return $min > $n ? $min : ($max < $n ? $max : $n);
}

function recombine($arr, $start, $end = null) {
	return implode(' ', $end === null ? array_slice($arr, $start) : array_slice($arr, $start, $end));
}
