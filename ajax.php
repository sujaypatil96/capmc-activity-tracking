<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

// Make the page publicly accessible
define("NOAUTH", true);

// Include base.php for all plugin constants and to connect to REDCap
// include_once(__DIR__ . "/base.php");

# Use only one of the following requires:
require('phpcap/autoloader.php'); # For Git 

use IU\PHPCap\RedCapProject;

$apiUrl = 'https://redcap.med.usc.edu/api/';  # replace this URL with your institution's
                                            # REDCap API URL.

$apiToken = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx';    # replace with your actual API token

$project = new RedCapProject($apiUrl, $apiToken);

// 'contact_1_crc' filter logic
$filter_1_logic = "([contact_1_date_time] > '";
$filter_1_logic .= $_GET["from_date"];
$filter_1_logic .= "') AND ([contact_1_date_time] < '";
$filter_1_logic .= $_GET["to_date"];
$filter_1_logic .= "')";

// 'contact_2_crc' filter logic
$filter_2_logic = "([contact_2_date_time] > '";
$filter_2_logic .= $_GET["from_date"];
$filter_2_logic .= "') AND ([contact_2_date_time] < '";
$filter_2_logic .= $_GET["to_date"];
$filter_2_logic .= "')";

// 'contact_3_crc' filter logic
$filter_3_logic = "([contact_3_date_time] > '";
$filter_3_logic .= $_GET["from_date"];
$filter_3_logic .= "') AND ([contact_3_date_time] < '";
$filter_3_logic .= $_GET["to_date"];
$filter_3_logic .= "')";

// 'contact_4_crc' filter logic
$filter_4_logic = "([contact_4_date_time] > '";
$filter_4_logic .= $_GET["from_date"];
$filter_4_logic .= "') AND ([contact_4_date_time] < '";
$filter_4_logic .= $_GET["to_date"];
$filter_4_logic .= "')";

$crc_keys = ['Carlos', 'Kanika', 'Catherine', 'CarlosH', 'Jennifer', 'Komal'];
$crc_values = ['0', '0', '0', '0', '0', '0'];

$contact_1_records = $project->exportRecordsAp(['fields' => ['contact_1_crc'], 'filterLogic' => $filter_1_logic]);

$contact_2_records = $project->exportRecordsAp(['fields' => ['contact_2_crc'], 'filterLogic' => $filter_2_logic]);

$contact_3_records = $project->exportRecordsAp(['fields' => ['contact_3_crc'], 'filterLogic' => $filter_3_logic]);

$contact_4_records = $project->exportRecordsAp(['fields' => ['contact_4_crc'], 'filterLogic' => $filter_4_logic]);

$contact_records = array_merge($contact_1_records, $contact_2_records, $contact_3_records, $contact_4_records);

$tot_attemps_arr = array_combine($crc_keys, $crc_values);

foreach ($contact_records as $key => $crc) {
	for ($i = 0; $i < count($crc_keys); $i++) { 
		if ((isset($crc['contact_1_crc']) && $crc['contact_1_crc'] == $crc_keys[$i])) {
			$tot_attemps_arr[$crc['contact_1_crc']]++; 
		}

		if ((isset($crc['contact_2_crc']) && $crc['contact_2_crc'] == $crc_keys[$i])) {
			$tot_attemps_arr[$crc['contact_2_crc']]++; 
		}

		if ((isset($crc['contact_3_crc']) && $crc['contact_3_crc'] == $crc_keys[$i])) {
			$tot_attemps_arr[$crc['contact_3_crc']]++; 
		}

		if ((isset($crc['contact_4_crc']) && $crc['contact_4_crc'] == $crc_keys[$i])) {
			$tot_attemps_arr[$crc['contact_4_crc']]++; 
		}
	}
}

$final_arr = array();
foreach ($tot_attemps_arr as $crc => $attempts_count) {
	array_push($final_arr, ['crc' => $crc, 'attempts_count' => $attempts_count]);
}

// Total calls that resulted in contact
$contact_1_outcomes_logic = "([contact_1_outcomes] = \"called_spoke_with_participant\") AND " . $filter_1_logic;
$contact_2_outcomes_logic = "([contact_2_outcomes] = \"called_spoke_with_participant\") AND " . $filter_2_logic;
$contact_3_outcomes_logic = "([contact_3_outcomes] = \"called_spoke_with_participant\") AND " . $filter_3_logic;
$contact_4_outcomes_logic = "([contact_4_outcomes] = \"called_spoke_with_participant\") AND " . $filter_4_logic;

$contact_1_outcomes_records = $project->exportRecordsAp(['fields' => ['contact_1_crc'], 'filterLogic' => $contact_1_outcomes_logic]);
$contact_2_outcomes_records = $project->exportRecordsAp(['fields' => ['contact_2_crc'], 'filterLogic' => $contact_2_outcomes_logic]);
$contact_3_outcomes_records = $project->exportRecordsAp(['fields' => ['contact_3_crc'], 'filterLogic' => $contact_3_outcomes_logic]);
$contact_4_outcomes_records = $project->exportRecordsAp(['fields' => ['contact_4_crc'], 'filterLogic' => $contact_4_outcomes_logic]);

$contact_outcomes_merge = array_merge($contact_1_outcomes_records, $contact_2_outcomes_records, $contact_3_outcomes_records, $contact_4_outcomes_records);

$tot_outcomes_arr = array_combine($crc_keys, $crc_values);

foreach ($contact_outcomes_merge as $key => $crc) {
	for ($i = 0; $i < count($crc_keys); $i++) { 
		if ((isset($crc['contact_1_crc']) && $crc['contact_1_crc'] == $crc_keys[$i])) {
			$tot_outcomes_arr[$crc['contact_1_crc']]++; 
		}

		if ((isset($crc['contact_2_crc']) && $crc['contact_2_crc'] == $crc_keys[$i])) {
			$tot_outcomes_arr[$crc['contact_2_crc']]++; 
		}

		if ((isset($crc['contact_3_crc']) && $crc['contact_3_crc'] == $crc_keys[$i])) {
			$tot_outcomes_arr[$crc['contact_3_crc']]++; 
		}

		if ((isset($crc['contact_4_crc']) && $crc['contact_4_crc'] == $crc_keys[$i])) {
			$tot_outcomes_arr[$crc['contact_4_crc']]++; 
		}
	}
}

$tot_outcomes_arr = array_values($tot_outcomes_arr);

for ($i = 0; $i < count($final_arr); $i++) {
	$final_arr[$i]['contact_outcomes'] = $tot_outcomes_arr[$i];
}

// Total voicemail
$contact_1_vm_logic = "([contact_1_outcomes] = \"called_left_voicemail\") AND " . $filter_1_logic;
$contact_2_vm_logic = "([contact_2_outcomes] = \"called_left_voicemail\") AND " . $filter_2_logic;
$contact_3_vm_logic = "([contact_3_outcomes] = \"called_left_voicemail\") AND " . $filter_3_logic;
$contact_4_vm_logic = "([contact_4_outcomes] = \"called_left_voicemail\") AND " . $filter_4_logic;

$contact_1_vm_records = $project->exportRecordsAp(['fields' => ['contact_1_crc'], 'filterLogic' => $contact_1_vm_logic]);
$contact_2_vm_records = $project->exportRecordsAp(['fields' => ['contact_2_crc'], 'filterLogic' => $contact_2_vm_logic]);
$contact_3_vm_records = $project->exportRecordsAp(['fields' => ['contact_3_crc'], 'filterLogic' => $contact_3_vm_logic]);
$contact_4_vm_records = $project->exportRecordsAp(['fields' => ['contact_4_crc'], 'filterLogic' => $contact_4_vm_logic]);

$contact_vm_merge = array_merge($contact_1_vm_records, $contact_2_vm_records, $contact_3_vm_records, $contact_4_vm_records);

$tot_vm_arr = array_combine($crc_keys, $crc_values);

foreach ($contact_vm_merge as $key => $crc) {
	for ($i = 0; $i < count($crc_keys); $i++) {
		if ((isset($crc['contact_1_crc']) && $crc['contact_1_crc'] == $crc_keys[$i])) {
			$tot_vm_arr[$crc['contact_1_crc']]++; 
		}

		if ((isset($crc['contact_2_crc']) && $crc['contact_2_crc'] == $crc_keys[$i])) {
			$tot_vm_arr[$crc['contact_2_crc']]++; 
		}

		if ((isset($crc['contact_3_crc']) && $crc['contact_3_crc'] == $crc_keys[$i])) {
			$tot_vm_arr[$crc['contact_3_crc']]++; 
		}

		if ((isset($crc['contact_4_crc']) && $crc['contact_4_crc'] == $crc_keys[$i])) {
			$tot_vm_arr[$crc['contact_4_crc']]++; 
		}
	}
}

$tot_vm_arr = array_values($tot_vm_arr);

for ($i = 0; $i < count($final_arr); $i++) {
	$final_arr[$i]['vm_outcomes'] = $tot_vm_arr[$i];
}

// Self complete consent form
$contact_1_consent_logic = "([contact_1_outcomes] = \"called_spoke_with_participant\") AND [self_complete_consent_forms] = \"1\" AND " . $filter_1_logic;
$contact_2_consent_logic = "([contact_2_outcomes] = \"called_spoke_with_participant\") AND [self_complete_consent_forms] = \"1\" AND " . $filter_2_logic;
$contact_3_consent_logic = "([contact_3_outcomes] = \"called_spoke_with_participant\") AND [self_complete_consent_forms] = \"1\" AND " . $filter_3_logic;
$contact_4_consent_logic = "([contact_4_outcomes] = \"called_spoke_with_participant\") AND [self_complete_consent_forms] = \"1\" AND " . $filter_4_logic;

$contact_1_consent_records = $project->exportRecordsAp(['fields' => ['contact_1_crc'], 'filterLogic' => $contact_1_consent_logic]);
$contact_2_consent_records = $project->exportRecordsAp(['fields' => ['contact_2_crc'], 'filterLogic' => $contact_2_consent_logic]);
$contact_3_consent_records = $project->exportRecordsAp(['fields' => ['contact_3_crc'], 'filterLogic' => $contact_3_consent_logic]);
$contact_4_consent_records = $project->exportRecordsAp(['fields' => ['contact_4_crc'], 'filterLogic' => $contact_4_consent_logic]);

$contact_consent_merge = array_merge($contact_1_consent_records, $contact_2_consent_records, $contact_3_consent_records, $contact_4_consent_records);

$tot_consent_arr = array_combine($crc_keys, $crc_values);

foreach ($contact_consent_merge as $key => $crc) {
	for ($i = 0; $i < count($crc_keys); $i++) { 
		if ((isset($crc['contact_1_crc']) && $crc['contact_1_crc'] == $crc_keys[$i])) {
			$tot_consent_arr[$crc['contact_1_crc']]++; 
		}

		if ((isset($crc['contact_2_crc']) && $crc['contact_2_crc'] == $crc_keys[$i])) {
			$tot_consent_arr[$crc['contact_2_crc']]++; 
		}

		if ((isset($crc['contact_3_crc']) && $crc['contact_3_crc'] == $crc_keys[$i])) {
			$tot_consent_arr[$crc['contact_3_crc']]++; 
		}

		if ((isset($crc['contact_4_crc']) && $crc['contact_4_crc'] == $crc_keys[$i])) {
			$tot_consent_arr[$crc['contact_4_crc']]++; 
		}
	}
}

$tot_consent_arr = array_values($tot_consent_arr);

for ($i = 0; $i < count($final_arr); $i++) {
	$final_arr[$i]['consent_outcomes'] = $tot_consent_arr[$i];
}

$arr = array('data' => $final_arr);

echo json_encode($arr);
?>