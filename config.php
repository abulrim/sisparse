<?php

// name of the institution: AUB, LAU, AUC...
$GLOBALS['institution'] = 'AUC';

// start text of the html table
$GLOBALS['tableStart'] = ($GLOBALS['institution'] == 'LAU') ? '<table  class="datadisplaytable"' : '<table class="datadisplaytable"';

// for multiple campuses use associative array
$GLOBALS['institutionIds'] = array(1);

// current term
$GLOBALS['term'] = 'Fall 2014';

// corresponding column number
$GLOBALS['rowNumbers'] = array(
	'AUB' => array(
		'crn' => 1,
		'code' => 2,
		'number' => 3,
		'section' => 4,
		'title' => 7,
		'days' => 8,
		'time' => 9,
		'instructor' => 19,
		'location' => 21
	),
	'LAU' => array(
		'crn' => 1,
		'code' => 2,
		'number' => 3,
		'section' => 4,
		'institution' => 5,
		'title' => 7,
		'days' => 8,
		'time' => 9,
		'instructor' => 19,
		'location' => 21
	),
	'AUC' => array(
		'crn' => 0,
		'code' => 1,
		'number' => 2,
		'section' => 3,
		'title' => 6,
		'days' => 7,
		'time' => 8,
		'instructor' => 12,
		'location' => 14
	)
);

?>
