<?php

require '../src/autoload.php';

$args = array('name' => 'myInput_01', 'label' => 'myLabel_01', 'data-mongars' => 'vnoez<ho"jkljlk"ihoi>hvioezh');
$input = new InputTextField();
$input->prepare($args)->render()->display();

//InputTextField::prepare($args)->render()->display();