<?php

/*
	Sort the results
*/
function cmp($a, $b) {
	if ($a->count == $b->count) {
		return 0;
	}
	return ($a->count > $b->count) ? -1 : 1;
}
usort($data->results, 'cmp');

/*
	put the results as percentages in an array
*/
$x=1;
$rows = array();
foreach($data->results as $result) {
	$rows[] = array(
		'rank'=>$x++,
		'link'=>$result->link,
		'source'=>htmlspecialchars($result->source, ENT_QUOTES, 'UTF-8'),
		'percent'=>number_format(($result->count/$data->total)*100, 3)
	);
};
echo json_encode($rows);
unset($rows);
?>