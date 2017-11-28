<?php
/* PHP CSV UPDATER
* PHP v7.1.0
* By: Maureen Davey, davey.maureen@gmail.com
* Purpose: Reformat a column in a CSV file to the intended format and export
* the updated document.
*/

$file = './input.csv';
$new_data = [];

$original_data = array_map('str_getcsv', file($file));

foreach($original_data as $line) {
	//CSV is in the format [0] date, [1] start time, [2] end time...[7] task
	//Rewrite to be in the format [0] start datetime, [1] end datetime, [2] task
	//If [0] is blank, use the previous date

	if(strlen($line[0]) <= 0) {
		$date = $previous_date;
	} else {
		$date = $line[0];
		$previous_date = $date; //for the next loop
	}

	//Create new start datetime
	$date_object = DateTime::createFromFormat('l, n/j/y g:i A', $previous_date . ' ' . $line[1]);
	$start_datetime = $date_object->format('n/j/Y g:ia');

	//Create new end datetime
	$date_object = DateTime::createFromFormat('l, n/j/y g:i A', $previous_date . ' ' . $line[2]);
	$end_datetime = $date_object->format('n/j/Y g:ia');

	//New CSV should be in the format start_datetime, end_datetime, task
	$new_line = array($start_datetime, $end_datetime, $line[7]);

	array_push($new_data, $new_line);
}

//Write reformatted data to a new CSV
$new_file = fopen('./output.csv', 'w');

foreach($new_data as $current_line) {
	fputcsv($new_file, $current_line, ',');
}

fclose($new_file);