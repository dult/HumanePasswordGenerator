<?php

require( 'hpg.php' );

$length = 2;
$complexity = 1;

if ( isset($_GET['l']) ) {
	$length = $_GET['l'];
} 
if ( isset($_GET['c']) ) {
	$complexity = $_GET['c'];
} 

$password = new HPG();
echo $password->generate( './eowl.sqlite', $length, $complexity );