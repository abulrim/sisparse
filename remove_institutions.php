<?php
	
//run indefinitely
set_time_limit(0);

//db configuration
require_once 'database.php';

$institutionIds = array(1);

$subjects = ORM::for_table('subjects')->where_in('institution_id', $institutionIds)->find_many();

foreach ($subjects as $subject) {
	$courses = ORM::for_table('courses')->where('subject_id', $subject->id)->find_many();
	foreach ($courses as $course) {
		// delete course slots
		ORM::for_table('course_slots')->where('course_id', $course->id)->delete_many();

		// delete course
		$course->delete();
	}

	// delete subject
	$subject->delete();
}

// delete institutions
ORM::for_table('institutions')->where_in('id', $institutionIds)->delete_many();


// clean instructors
$instructors = ORM::for_table('instructors')->find_many();

foreach ($instructors as $instructor) {
	$course_slot = ORM::for_table('course_slots')->where('instructor_id', $instructor->id)->find_one();

	if (!$course_slot) {
		$instructor->delete();
	}
}
?>