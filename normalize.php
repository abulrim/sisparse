<?php

set_time_limit(0);

function checkInstructor($name) {
	$instructorResult = mysql_query("SELECT * FROM instructors WHERE name='$name'");
	if ($instructorResult && mysql_num_rows($instructorResult)) {
		$instructors = mysql_fetch_assoc($instructorResult);
		$instructor_id = $instructors['id'];
	} else {
		mysql_query("INSERT INTO instructors (name) VALUES ('$name')");
		$instructor_id = mysql_insert_id();
	}
	return $instructor_id;
}

function checkSubjects($code) {
	$subjectResult = mysql_query("SELECT * FROM subjects WHERE code='$code'");
	if (mysql_num_rows($subjectResult)) {
		$subjects = mysql_fetch_assoc($subjectResult);
		$subject_id = $subjects['id'];
	} else {
		mysql_query("INSERT INTO subjects (code) VALUES ('$code')");
		$subject_id = mysql_insert_id();
	}
	return $subject_id;
}

//connect to db
include('database.php');

$result = mysql_query("SELECT * FROM courses_");

while ($row = mysql_fetch_array($result)) {
	$code = $row['subject'];
	$subject_id = checkSubjects($code);
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


	mysql_query("INSERT INTO courses (term, crn, subject_id, number, section, title, m, t, w, r, f, s) 
				VALUES ('$term','$crn','$subject_id','$number','$section','$title', '$mon', '$tue', '$wed', '$thu', '$fri', '$sat')");

	$course_id = mysql_insert_id();

	$instructor_1 = $row['instructor_1'];
	$instructor_2 = $row['instructor_2'];

	$instructor_id_1 = checkInstructor($instructor_1);
	$instructor_id_2 = checkInstructor($instructor_2);

	$days = array($row['m_1'], $row['t_1'], $row['w_1'], $row['r_1'], $row['f_1'], $row['sat_1']);

	$startTime = $row['begin_time_1'];
	$endTime = $row['end_time_1'];
	$building = $row['building_1'];
	$room = $row['room_1'];

	foreach ($days as $key => $day) {
		$dd = $key + 1;
		if ($day == 1) {
			mysql_query("INSERT INTO course_slots (start_time, end_time, building, room, instructor_id, day, course_id) 
					VALUES ('$startTime','$endTime','$building','$room','$instructor_id_1','$dd','$course_id')");
		}
	}

	$days = array($row['m_2'], $row['t_2'], $row['w_2'], $row['r_2'], $row['f_2'], $row['sat_2']);

	$startTime = $row['begin_time_2'];
	$endTime = $row['end_time_2'];
	$building = $row['building_2'];
	$room = $row['room_2'];

	foreach ($days as $key => $day) {
		$dd = $key + 1;
		if ($day == 1) {
			mysql_query("INSERT INTO course_slots (start_time, end_time, building, room, instructor_id, day, course_id) 
					VALUES ('$startTime','$endTime','$building','$room','$instructor_id_2','$dd','$course_id')");
		}
	}
}
?>