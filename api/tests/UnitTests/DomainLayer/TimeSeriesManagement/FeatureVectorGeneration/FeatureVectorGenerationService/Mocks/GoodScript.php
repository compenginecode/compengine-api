<?php

$dataPoints = explode(",", $argv[1]);

$featureVector = [];
for($i = 0; $i < 3; $i++){
	$featureVector[$i] = $i . ":" . $i;
}

echo implode(",", $featureVector);