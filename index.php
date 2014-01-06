<?php

define('INSTITUTION', 'lau');  
define('INSTITUTION_ID', 2);  
define('TERM', 'Spring 2014');  

function cleanField($field) {
	//decode any html character
	$field = html_entity_decode($field, ENT_COMPAT, 'ISO-8859-1');

	//replace white space char to ' '
	//http://stackoverflow.com/questions/6275380/does-html-entity-decode-replaces-nbsp-also-if-not-how-to-replace-it
	$field = str_replace("\xA0", ' ', $field);

	//remove html characters
	$field = strip_tags($field);

	//remove whitespace
	$field = trim($field);
	return $field;
}

function parseTime($time) {
	if (strtolower($time) == 'tba' || empty($time)) {
		return array(NULL, NULL);
	}

	$parsedTime = explode('-', $time);

	if (count($parsedTime) !== 2) {
		return array(NULL, NULL);		
	}

	$parsedTime[0] = date('H:i:s', strtotime($parsedTime[0]));
	$parsedTime[1] = date('H:i:s', strtotime($parsedTime[1]));

	return $parsedTime;
}

function parseDays($days) {
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

	return array($m, $t, $w, $r, $f, $sat);
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

// checks the existance of a model with a certain property in table and returns
// id of found ot created model
function checkExistance($tableName, $propertyName, $propertyValue) {
	if (!$propertyValue) {
		$propertyValue = 'TBA';
	}

	if ($tableName == 'subjects') {
		$model = ORM::for_table($tableName)
					->where('institution_id', INSTITUTION_ID)
					->where($propertyName, $propertyValue)
					->find_one();	
	} else {
		$model = ORM::for_table($tableName)->where($propertyName, $propertyValue)->find_one();	
	}

	if (!$model) {
		$model = ORM::for_table($tableName)->create();
		$model->set($propertyName, $propertyValue);
		if ($tableName == 'subjects') {
			$model->set('institution_id', INSTITUTION_ID);
		}
		$model->save();
	}
	return $model->id;
}

function insertCourseSlot($instructor, $days, $startTime, $endTime, $building, $room, $courseId) {
	// return if any day null
	foreach ($days as $day) {
		if ($day == null) {
			break;
			return;
		}
	}

	if (!$startTime or !$endTime or !$courseId) {
		return;
	}
	if (!$building) {
		$building = 'TBA';
	}
	if (!$room) {
		$room = 'TBA';
	}

	$instructorId = checkExistance('instructors', 'name', $instructor);

	foreach ($days as $key => $day) {
		$dd = $key + 1;
		if ($day == 1) {
			$courseSlot = ORM::for_table('course_slots')->create();
			$courseSlot->set(array(
				'start_time' => $startTime,
				'end_time' => $endTime,
				'building' => $building,
				'room' => $room,
				'instructor_id' => $instructorId,
				'day' => $dd,
				'course_id' => $courseId
			));
			$courseSlot->save();

		}
	}

}

function handleRow($row, $lastCourseId = false) {
	$crn = $row[1];
	$code = $row[2];
	$number = $row[3];
	$section = $row[4];
	$title = $row[7];
	$days = parseDays($row[8]);
	$instructor = $row[19];

	// fix time
	$time = parseTime($row[9]);
	$startTime = $time[0];
	$endTime = $time[1];

	// fix building
	$location = parseLocation($row[21]);
	$building = $location[0];
	$room = $location[1];

	if (!empty($crn)) {
		$subjectId = checkExistance('subjects', 'code', $code);

		$course = ORM::for_table('courses')->create();
		$course->set(array(
			'term' => TERM,
			'crn' => $crn,
			'subject_id' => $subjectId,
			'number' => $number,
			'section' => $section,
			'title' => $title
		));
		$course->save();
		$lastCourseId = $course->id;
	}

	insertCourseSlot(
		$instructor,
		$days,
		$startTime,
		$endTime,
		$building,
		$room,
		$lastCourseId
	);

	return $lastCourseId;
}

function parse($text) {
	//extract the right table
	$startTable = stripos($text, '<table  class="datadisplaytable"');
	$endTable = stripos($text, '</table>', $startTable) + 8;
	$text = substr($text, $startTable, $endTable - $startTable);

	//start parsing (loop through all rows)
	$start = stripos($text, '<tr');

	while ($start !== false) {
		$lastCourseId = false;
		$end = stripos($text, '</tr>', $start) + 5;
		$offset = $end - $start;

		$tr = substr($text, $start, $offset);
		$text = substr($text, $end);

		//loop through all columns and insert each cell in $rowContent
		$rowContent = array();
		$colStart = stripos($tr, '<td');
		while ($colStart !== false) {
			$colEnd = stripos($tr, '</td>', $colStart) + 5;
			$colOffset = $colEnd - $colStart;
			$cell = substr($tr, $colStart, $colOffset);

			$rowContent[] = cleanField($cell);

			// colspan fix
			if (stripos($cell, 'colspan="2"')) {
				$rowContent[] = '';
			}

			$colStart = stripos($tr, '<td', $colEnd);
		}

		if (!empty($rowContent)) {
			$lastCourseId = handleRow($rowContent, $lastCourseId);
		}
		$start = stripos($text, '<tr');
	}
}

//run indefinitely
set_time_limit(0);

//db configuration
require_once 'database.php';

//select the file
$text = file_get_contents("courses.html");
parse($text);

?>
