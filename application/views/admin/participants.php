<?php 

$selectArray = array();
foreach ($participants as $participant) {
	array_push($selectArray, anchor('participant/'.$participant->uuid, $participant->lastName . ', ' . $participant->firstName));
}

print ul($selectArray);

?>