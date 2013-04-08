<?php

//generic function

function cleanField($field) {
	//decode any html character
	$field = html_entity_decode($field);

	//replace white space char to ' '
	//http://stackoverflow.com/questions/6275380/does-html-entity-decode-replaces-nbsp-also-if-not-how-to-replace-it
	$field = str_replace("\xA0", ' ', $field);

	//remove html characters
	$field = strip_tags($field);

	//escape field
	$field = mysql_real_escape_string($field);

	//remove whitespace
	$field = trim($field);
	return $field;
}

function parseTime($time) {
	if (strtolower($time) == 'tba') {
		return array(NULL, NULL);
	}
	if (empty($time)) {
		return array(NULL, NULL);
	}
	$parsedTime = array();
	$parsedTime = explode('-', $time);

	$parsedTime[0] = date('H:i:s', strtotime($parsedTime[0]));
	$parsedTime[1] = date('H:i:s', strtotime($parsedTime[1]));

	return $parsedTime;
}

function parseLocation($location) {
	if (strtolower($location) == 'tba') {
		return array('TBA', NULL);
	}
	$parsedLocation = array();
	$parsedLocation = explode(' ', $location);
	if (count($parsedLocation) == 1) {
		return array($parsedLocation[0], NULL);
	}
	return $parsedLocation;
}

function addEl($array, $el, $pos) {
	return array_merge(array_slice($array, 0, $pos), array($el), array_slice($array, $pos));
}

//run indefinitely
set_time_limit(0);

//connect to db
include('database.php');

//select the file
$text = file_get_contents("courses.html");

//extract the right table
$startTable = stripos($text, '<table class="datadisplaytable"');
$text = substr($text, $startTable);

//start parsing (loop through all rows)
$insertId = null;
$start = stripos($text, '<tr');
while ($start !== false) {
	$end = stripos($text, '</tr>', $start) + 5;
	$offset = $end - $start;

	$tr = substr($text, $start, $offset);

	//loop through all columns
	$rowContent = array();
	$colStart = stripos($tr, '<td');
	while ($colStart !== false) {
		$colEnd = stripos($tr, '</td>', $colStart) + 5;
		$colOffset = $colEnd - $colStart;

		$rowContent[] = cleanField(substr($tr, $colStart, $colOffset));

		$colStart = stripos($tr, '<td', $colEnd);
	}

	if (empty($rowContent[1]) && !empty($rowContent)) {
		array_unshift($rowContent, '');
	}
	if (count($rowContent) == 23) {
		$rowContent = addEl($rowContent, '', 10);
	}

	if (!empty($rowContent)) {

		$crn = $rowContent[1];

		echo $crn . '<br>';

		$code = $rowContent[2];
		$number = $rowContent[3];
		$section = $rowContent[4];
		$title = $rowContent[7];
		$instructor = $rowContent[20];

		//fix time
		$time = parseTime($rowContent[10]);
		$startTime = $time[0];
		$endTime = $time[1];

		//fix building
		$location = parseLocation($rowContent[22]);
		$building = $location[0];
		$room = $location[1];

		$days = $rowContent[9];
		if (strtolower($days) == 'tba') {
			$days = '';
		}
		$m = $t = $w = $r = $f = $sat = $sun = 0;

		if (stripos($days, 'M') !== false) {
			$m = 1;
		}
		if (stripos($days, 'T') !== false) {
			$t = 1;
		}
		if (stripos($days, 'W') !== false) {
			$w = 1;
		}
		if (stripos($days, 'R') !== false) {
			$r = 1;
		}
		if (stripos($days, 'F') !== false) {
			$f = 1;
		}
		if (stripos($days, 'S') !== false) {
			$sat = 1;
		}

		if (!empty($rowContent[1])) {
			mysql_query("INSERT INTO courses_ (
                    term, 
                    crn, 
                    subject, 
                    course, 
                    section, 
                    title, 
                    begin_time_1, 
                    end_time_1, 
                    building_1, 
                    room_1, 
                    m_1, 
                    t_1, 
                    w_1, 
                    r_1, 
                    f_1, 
                    sat_1, 
                    sun_1, 
                    instructor_1
                ) VALUES (
                    '201320', 
                    '$crn',
                    '$code',
                    '$number',
                    '$section',
                    '$title',
                    '$startTime',
                    '$endTime',
                    '$building',
                    '$room',
                    '$m',
                    '$t',
                    '$w',
                    '$r',
                    '$f',
                    '$sat',
                    '$sun',
                    '$instructor'
                )");
			$insertId = mysql_insert_id();
			if ($startTime == NULL) {
				mysql_query("UPDATE courses_ SET begin_time_1=NULL, end_time_1=NULL WHERE id='$insertId'");
			}
		} else {
			mysql_query("UPDATE courses_ SET 
                    begin_time_2='$startTime', 
                    end_time_2='$endTime', 
                    building_2='$building', 
                    room_2='$room', 
                    m_2='$m', 
                    t_2='$t', 
                    w_2='$w', 
                    r_2='$r', 
                    f_2='$f', 
                    sat_2='$sat', 
                    sun_2='$sun', 
                    instructor_2='$instructor'
                    WHERE id='$insertId'");
			if ($startTime == NULL) {
				mysql_query("UPDATE courses_ SET begin_time_2=NULL, end_time_2=NULL WHERE id='$insertId'");
			}
		}
	}
	$start = stripos($text, '<tr', $end);
}
?>