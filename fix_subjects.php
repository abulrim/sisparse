<?php
	$subject = array();
	$subject["ACCT"] = "Accounting";
	$subject["AGSC"] = "Agricultural Science";
	$subject["AMST"] = "American Studies";
	$subject["AVSC"] = "Animal and Veterinary Sciences";
	$subject["ARAB"] = "Arabic";
	$subject["AROL"] = "Archaeology";
	$subject["ARCH"] = "Architecture";
	$subject["BIOC"] = "Biochemistry";
	$subject["BIOL"] = "Biology";
	$subject["BUSS"] = "Business";
	$subject["CHEN"] = "Chemical Engineering";
	$subject["CHEM"] = "Chemistry";
	$subject["CHIN"] = "Chinese";
	$subject["CIVE"] = "Civil &amp; Environmental  Eng'g";
	$subject["CVSP"] = "Civilization Sequence";
	$subject["CMTS"] = "Computational Science";
	$subject["CMPS"] = "Computer Science";
	$subject["DCSN"] = "Decision System";
	$subject["ECON"] = "Economics";
	$subject["EDUC"] = "Education";
	$subject["EECE"] = "Electrical &amp; Computer Eng'g";
	$subject["ENMG"] = "Engineering Management";
	$subject["ENGL"] = "English";
	$subject["ENTM"] = "Entrepreneurship";
	$subject["ENHL"] = "Environmental Health";
	$subject["ENSC"] = "Environmental Science";
	$subject["EPHD"] = "Epidemiology &amp; Population Hlth";
	$subject["FINA"] = "Finance";
	$subject["FAAH"] = "Fine Arts &amp; Arts History";
	$subject["FREN"] = "French";
	$subject["GEOL"] = "Geology";
	$subject["GRDS"] = "Graphic Design";
	$subject["HMPD"] = "Health Management &amp; Policy";
	$subject["HPCH"] = "Health Promot&amp; Community Healt";
	$subject["HIST"] = "History";
	$subject["HUMR"] = "Human Morphology";
	$subject["INFO"] = "Information Systems";
	$subject["IDTH"] = "Interdepartmental Teaching";
	$subject["LABM"] = "Laboratory Medicine";
	$subject["LDEM"] = "Landscape Design &amp; Eco-Managmn";
	$subject["INFP"] = "MBA Integrative Foundat.Period";
	$subject["MNGT"] = "Management";
	$subject["MKTG"] = "Marketing";
	$subject["MHRM"] = "Mast.in Human Resources Managm";
	$subject["MFIN"] = "Master in Finance";
	$subject["MATH"] = "Mathematics";
	$subject["MECH"] = "Mechanical Engineering";
	$subject["MCOM"] = "Media Studies";
	$subject["MLSP"] = "Medical Laboratory Sciences";
	$subject["MBIM"] = "Microbiology &amp; Immunology";
	$subject["MEST"] = "Middle Eastern Studies";
	$subject["NURS"] = "Nursing";
	$subject["NFSC"] = "Nutrition &amp; Food Science";
	$subject["PHRM"] = "Pharmacology and Therapeutics";
	$subject["PHIL"] = "Philosophy";
	$subject["PHYS"] = "Physics";
	$subject["PHYL"] = "Physiology";
	$subject["PSPA"] = "Political Stud &amp; Public Adm";
	$subject["PSYC"] = "Psychology";
	$subject["PBHL"] = "Public Health";
	$subject["XRAY"] = "Radiologic Technology";
	$subject["SOAN"] = "Sociology-Anthropology";
	$subject["STAT"] = "Statistics";
	$subject["EXPR"] = "Study Abroad";
	$subject["URDS"] = "Urban Design";
	$subject["URPL"] = "Urban Planning";
	
	$con = mysql_connect("localhost","root","");
	if (!$con) {
		die('Could not connect: ' . mysql_error());
	}
	
	mysql_select_db("sis", $con);
	
	foreach($subject as $key => $s) {
		$s = str_replace("'", "&#39;", $s);
		mysql_query("UPDATE subjects SET name='$s' WHERE code='$key'");
	}
?>