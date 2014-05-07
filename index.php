<?php

require_once 'subjects.php';
require_once 'config.php';
require_once 'functions.php';
require_once 'database.php';

//run indefinitely
set_time_limit(0);

//select the file
$text = file_get_contents('courses.html');
parse($text);
fixCourseDays();
fixSubjects();

?>
