<?php

//connect to db
require_once 'database.php';

//run indefinitely
set_time_limit(0);

// checks the existance of a model with a certain property in table and returns
// id of found ot created model
function checkExistance($tableName, $propertyName, $propertyValue) {

	if (!$propertyValue) {
		$propertyValue = 'TBA';
	}

	$model = ORM::for_table($tableName)->where($propertyName, $propertyValue)->find_one();

	if (!$model) {
		$model = ORM::for_table($tableName)->create();
		$model->set($propertyName, $propertyValue);
		$model->save();
	}
	return $model->id;

}

function insertCourseSlot($instructor, $days, $startTime, $endTime, $building, $room, $course_id) {

	// return if any day null
	foreach ($days as $day) {
		if ($day == null) {
			break;
			return;
		}
	}

	if (!$startTime or !$endTime or !$course_id) {
		return;
	}
	if (!$building) {
		$building = 'TBA';
	}
	if (!$room) {
		$room = 'TBA';
	}

	$instructor_id = checkExistance('instructors', 'name', $instructor);

	foreach ($days as $key => $day) {
		$dd = $key + 1;
		if ($day == 1) {

			$courseSlot = ORM::for_table('course_slots')->create();
			$courseSlot->set(array(
				'start_time' => $startTime,
				'end_time' => $endTime,
				'building' => $building,
				'room' => $room,
				'instructor_id' => $instructor_id,
				'day' => $dd,
				'course_id' => $course_id
			));
			$courseSlot->save();

		}
	}

}

$result = ORM::for_table('courses_')->find_array();

foreach ($result as $row) {
	$code = $row['subject'];
	$subject_id = checkExistance('subjects', 'code', $code);
	$term = $row['term'];
	$crn = $row['crn'];
	$number = $row['course'];
	$section = $row['section'];
	$title = $row['title'];
	$mon = $row['m_1'] || $row['m_2'];
	$tue = $row['t_1'] || $row['t_2'];
	$wed = $row['w_1'] || $row['w_2'];
	$thu = $row['r_1'] || $row['r_2'];
	$fri = $row['f_1'] || $row['f_2'];
	$sat = $row['sat_1'] || $row['sat_2'];

	// insert course
	$course = ORM::for_table('courses')->create();
	$course->set(array(
		'term' => $term,
		'crn' => $crn,
		'subject_id' => $subject_id,
		'number' => $number,
		'section' => $section,
		'title' => $title,
		'm' => $mon,
		't' => $tue,
		'w' => $wed,
		'r' => $thu,
		'f' => $fri,
        's' => $sat
	));
	$course->save();

	// insert first course slot
	insertCourseSlot(
		$row['instructor_1'],
		array(
			$row['m_1'],
			$row['t_1'],
			$row['w_1'],
			$row['r_1'],
			$row['f_1'],
			$row['sat_1']
		),
		$row['begin_time_1'],
		$row['end_time_1'],
		$row['building_1'],
		$row['room_1'],
		$course->id
	);

	// insert second course slot
	insertCourseSlot(
		$row['instructor_2'],
		array(
			$row['m_2'],
			$row['t_2'],
			$row['w_2'],
			$row['r_2'],
			$row['f_2'],
			$row['sat_2']
		),
		$row['begin_time_2'],
		$row['end_time_2'],
		$row['building_2'],
		$row['room_2'],
		$course->id
	);
}
?>
