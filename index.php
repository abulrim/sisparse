<?php

$GLOBALS['institution'] = 'AUB';
$GLOBALS['institutionId'] = 2;
$GLOBALS['institutionIdBeirut'] = 2;
$GLOBALS['institutionIdJbeil'] = 3;
$GLOBALS['term'] = 'Summer 2014';

if ($GLOBALS['institution'] == 'AUB') {
	$GLOBALS['institutionIds'] = array($GLOBALS['institutionId']);
} else {
	$GLOBALS['institutionIds'] = array(
		$GLOBALS['institutionIdBeirut'],
		$GLOBALS['institutionIdJbeil']
	);	
}

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
function checkExistance($tableName, $conditions = array()) {

	// Search for the model
	$model = ORM::for_table($tableName);
	foreach ($conditions as $name => $value) {
		if (!$value) {
			$value = 'TBA';
		}
		$model->where($name, $value);
	}
	$model = $model->find_one();

	// If model not found create it
	if (!$model) {
		$model = ORM::for_table($tableName)->create();
		foreach ($conditions as $name => $value) {
			$model->set($name, $value);
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

	$instructorId = checkExistance('instructors', array('name' => $instructor));

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

	if ($GLOBALS['institution'] == 'LAU') {
		// $row[5] corresponds to campus number 1 = LAU - beirut, 2 = LAU - Byblos
		if ($row[5] == 1) {
			$institutionId = $GLOBALS['institutionIdBeirut'];
		} else {
			$institutionId = $GLOBALS['institutionIdJbeil'];
		}
	} elseif ($GLOBALS['institution'] == 'AUB') {
		$institutionId = $GLOBALS['institutionId'];
	}

	$title = $row[7];
	$days = parseDays($row[8]);

	// fix time
	$time = parseTime($row[9]);
	$startTime = $time[0];
	$endTime = $time[1];

	$instructor = $row[19];

	// fix building
	$location = parseLocation($row[21]);
	$building = $location[0];
	$room = $location[1];

	if (!empty($crn)) {
		$subjectId = checkExistance(
			'subjects', 
			array(
				'code' => $code,
				'institution_id' => $institutionId
			)
		);

		$course = ORM::for_table('courses')->create();
		$course->set(array(
			'term' => $GLOBALS['term'],
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
	$lastCourseId = false;

	//extract the right table
	$startText = ($GLOBALS['institution'] == 'LAU') ? '<table  class="datadisplaytable"' : '<table class="datadisplaytable"';
	$startTable = stripos($text, $startText);
	$endTable = stripos($text, '</table>', $startTable) + 8;
	$text = substr($text, $startTable, $endTable - $startTable);

	//start parsing (loop through all rows)
	$start = stripos($text, '<tr');

	while ($start !== false) {
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
			if ($GLOBALS['institution'] == 'AUB') {
				if (empty($rowContent[1])) {
					array_unshift($rowContent, '');
				}
				unset($rowContent[8]);
				$rowContent = array_values($rowContent);
			}

			$lastCourseId = handleRow($rowContent, $lastCourseId);
		}
		$start = stripos($text, '<tr');
	}
}

function fixCourseDays() {
	$courses = ORM::for_table('courses')->find_many();

	foreach($courses as $course) {
		$days = array('m', 't', 'w', 'r', 'f', 's');

		$courseSlots = ORM::for_table('course_slots')->where('course_id', $course->id)->find_array();
		foreach($courseSlots as $courseSlot) {
			$course->set($days[$courseSlot['day'] - 1], 1);
		}

		$course->save();
	}
}

function fixSubjects() {
	$subjects = array(
		"AUB" => array(
			"ACCT" => "Accounting",
			"AGSC" => "Agricultural Science",
			"AMST" => "American Studies",
			"AVSC" => "Animal and Veterinary Sciences",
			"ARAB" => "Arabic",
			"AROL" => "Archaeology",
			"ARCH" => "Architecture",
			"BIOC" => "Biochemistry",
			"BIOL" => "Biology",
			"BUSS" => "Business",
			"CHEN" => "Chemical Engineering",
			"CHEM" => "Chemistry",
			"CHIN" => "Chinese",
			"CIVE" => "Civil &amp; Environmental  Eng&#39g",
			"CVSP" => "Civilization Sequence",
			"CMTS" => "Computational Science",
			"CMPS" => "Computer Science",
			"DCSN" => "Decision System",
			"ECON" => "Economics",
			"EDUC" => "Education",
			"EECE" => "Electrical &amp; Computer Eng&#39g",
			"ENMG" => "Engineering Management",
			"ENGL" => "English",
			"ENTM" => "Entrepreneurship",
			"ENHL" => "Environmental Health",
			"ENSC" => "Environmental Science",
			"EPHD" => "Epidemiology &amp; Population Hlth",
			"FINA" => "Finance",
			"FAAH" => "Fine Arts &amp; Arts History",
			"FREN" => "French",
			"GEOL" => "Geology",
			"GRDS" => "Graphic Design",
			"HMPD" => "Health Management &amp; Policy",
			"HPCH" => "Health Promot&amp; Community Healt",
			"HIST" => "History",
			"HUMR" => "Human Morphology",
			"INFO" => "Information Systems",
			"IDTH" => "Interdepartmental Teaching",
			"LABM" => "Laboratory Medicine",
			"LDEM" => "Landscape Design &amp; Eco-Managmn",
			"INFP" => "MBA Integrative Foundat.Period",
			"MNGT" => "Management",
			"MKTG" => "Marketing",
			"MHRM" => "Mast.in Human Resources Managm",
			"MFIN" => "Master in Finance",
			"MATH" => "Mathematics",
			"MECH" => "Mechanical Engineering",
			"MCOM" => "Media Studies",
			"MLSP" => "Medical Laboratory Sciences",
			"MBIM" => "Microbiology &amp; Immunology",
			"MEST" => "Middle Eastern Studies",
			"NURS" => "Nursing",
			"NFSC" => "Nutrition &amp; Food Science",
			"PHRM" => "Pharmacology and Therapeutics",
			"PHIL" => "Philosophy",
			"PHYS" => "Physics",
			"PHYL" => "Physiology",
			"PSPA" => "Political Stud &amp; Public Adm",
			"PSYC" => "Psychology",
			"PBHL" => "Public Health",
			"SHRP" => "SHARP",
			"XRAY" => "Radiologic Technology",
			"SOAN" => "Sociology-Anthropology",
			"STAT" => "Statistics",
			"EXPR" => "Study Abroad",
			"URDS" => "Urban Design",
			"URPL" => "Urban Planning"
		),
		"LAU" => array(
			"ACC" => "Accounting",
			"ARA" => "Arabic Studies",
			"ARC" => "Architecture",
			"FIN" => "Banking &amp; Finance",
			"BCH" => "Biochemistry",
			"BIO" => "Biology",
			"CHM" => "Chemistry",
			"CHN" => "Chinese",
			"CIE" => "Civil Engineering",
			"COM" => "Communication Arts",
			"CLT" => "Comparative Literature",
			"COE" => "Computer Engineering",
			"CSC" => "Computer Science",
			"CST" => "Cultural Studies",
			"ECO" => "Economics",
			"EDU" => "Education",
			"ELE" => "Electrical Engineering",
			"ENG" => "English",
			"ENV" => "Environmental Science",
			"ETH" => "Ethics",
			"FEM" => "Family &amp; Entrepren. Mgt.",
			"ART" => "Fine Arts",
			"FND" => "Foundation",
			"BUS" => "General Business",
			"GNE" => "General Engineering",
			"GER" => "German",
			"GRA" => "Graphic Design",
			"HLT" => "Health Science",
			"HST" => "History",
			"HOM" => "Hospitality Management",
			"INE" => "Industrial Engineering",
			"ITM" => "Inform. Technology Management",
			"DES" => "Interior Design",
			"INA" => "International Affairs",
			"IBS" => "International Business",
			"ITA" => "Italian",
			"LEG" => "Legal Studies",
			"MGT" => "Management",
			"MKT" => "Marketing",
			"MTH" => "Mathematics",
			"MEE" => "Mechanical Engineering",
			"MUS" => "Music",
			"NUR" => "Nursing",
			"NUT" => "Nutrition",
			"OPM" => "Operation &amp; Production Mgt.",
			"PHA" => "Pharmacy",
			"PHL" => "Philosophy",
			"PHO" => "Photography",
			"PED" => "Physical Education",
			"PHY" => "Physics",
			"POL" => "Political Science",
			"PSY" => "Psychology",
			"QBA" => "Quantitative Business Analysis",
			"REL" => "Religion",
			"SOC" => "Sociology",
			"SPA" => "Spanish",
			"SAR" => "Special Arabic",
			"STA" => "Statistics",
			"TRA" => "Translation",
			"WOS" => "Women Studies (Institute)"
		)
	);

	foreach($subjects[$GLOBALS['institution']] as $key => $subject) {

		$models = ORM::for_table('subjects')
					->where('code', $key)
					->where_in('institution_id', $GLOBALS['institutionIds'])
					->find_many();

		if ($models) {
			foreach ($models as $model) {
				$model->set('name', $subject);
				$model->save();
			}
		}
	}
}

//run indefinitely
set_time_limit(0);

//db configuration
require_once 'database.php';

//select the file
$text = file_get_contents("courses.html");
parse($text);
fixCourseDays();
fixSubjects();

?>
