<?php
	$subjects = array();
	$subjects["ACCT"] = "Accounting";
	$subjects["AGSC"] = "Agricultural Science";
	$subjects["AMST"] = "American Studies";
	$subjects["AVSC"] = "Animal and Veterinary Sciences";
	$subjects["ARAB"] = "Arabic";
	$subjects["AROL"] = "Archaeology";
	$subjects["ARCH"] = "Architecture";
	$subjects["BIOC"] = "Biochemistry";
	$subjects["BIOL"] = "Biology";
	$subjects["BUSS"] = "Business";
	$subjects["CHEN"] = "Chemical Engineering";
	$subjects["CHEM"] = "Chemistry";
	$subjects["CHIN"] = "Chinese";
	$subjects["CIVE"] = "Civil &amp; Environmental  Eng&#39g";
	$subjects["CVSP"] = "Civilization Sequence";
	$subjects["CMTS"] = "Computational Science";
	$subjects["CMPS"] = "Computer Science";
	$subjects["DCSN"] = "Decision System";
	$subjects["ECON"] = "Economics";
	$subjects["EDUC"] = "Education";
	$subjects["EECE"] = "Electrical &amp; Computer Eng&#39g";
	$subjects["ENMG"] = "Engineering Management";
	$subjects["ENGL"] = "English";
	$subjects["ENTM"] = "Entrepreneurship";
	$subjects["ENHL"] = "Environmental Health";
	$subjects["ENSC"] = "Environmental Science";
	$subjects["EPHD"] = "Epidemiology &amp; Population Hlth";
	$subjects["FINA"] = "Finance";
	$subjects["FAAH"] = "Fine Arts &amp; Arts History";
	$subjects["FREN"] = "French";
	$subjects["GEOL"] = "Geology";
	$subjects["GRDS"] = "Graphic Design";
	$subjects["HMPD"] = "Health Management &amp; Policy";
	$subjects["HPCH"] = "Health Promot&amp; Community Healt";
	$subjects["HIST"] = "History";
	$subjects["HUMR"] = "Human Morphology";
	$subjects["INFO"] = "Information Systems";
	$subjects["IDTH"] = "Interdepartmental Teaching";
	$subjects["LABM"] = "Laboratory Medicine";
	$subjects["LDEM"] = "Landscape Design &amp; Eco-Managmn";
	$subjects["INFP"] = "MBA Integrative Foundat.Period";
	$subjects["MNGT"] = "Management";
	$subjects["MKTG"] = "Marketing";
	$subjects["MHRM"] = "Mast.in Human Resources Managm";
	$subjects["MFIN"] = "Master in Finance";
	$subjects["MATH"] = "Mathematics";
	$subjects["MECH"] = "Mechanical Engineering";
	$subjects["MCOM"] = "Media Studies";
	$subjects["MLSP"] = "Medical Laboratory Sciences";
	$subjects["MBIM"] = "Microbiology &amp; Immunology";
	$subjects["MEST"] = "Middle Eastern Studies";
	$subjects["NURS"] = "Nursing";
	$subjects["NFSC"] = "Nutrition &amp; Food Science";
	$subjects["PHRM"] = "Pharmacology and Therapeutics";
	$subjects["PHIL"] = "Philosophy";
	$subjects["PHYS"] = "Physics";
	$subjects["PHYL"] = "Physiology";
	$subjects["PSPA"] = "Political Stud &amp; Public Adm";
	$subjects["PSYC"] = "Psychology";
	$subjects["PBHL"] = "Public Health";
	$subjects["SHRP"] = "SHARP";
	$subjects["XRAY"] = "Radiologic Technology";
	$subjects["SOAN"] = "Sociology-Anthropology";
	$subjects["STAT"] = "Statistics";
	$subjects["EXPR"] = "Study Abroad";
	$subjects["URDS"] = "Urban Design";
	$subjects["URPL"] = "Urban Planning";

	require_once 'database.php';

	foreach($subjects as $key => $subject) {

		$model = ORM::for_table('subjects')->where('code', $key)->find_one();
		if ($model) {
			$model->set('name', $subject);
			$model->save();
		}

	}
?>
