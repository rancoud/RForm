<?php

require '../src/autoload.php';

$args = array('name' => 'myInput_01', 'label' => 'myLabel_01', 'data-mongars' => 'vnoez<ho"jkljlk"ihoi>hvioezh', 'value' => 'ok"ez"z"rz"rzlm', 'maxlength' => 10);
$input = new InputTextField();
$input->prepare($args)->render()->display();

$options = array(
    array('myvalue' => 'mylabel'),
    array('223' => 'ccxxiii', 'attrs' => array('data-livecoding-viewer' => 'true')),
    array('michel' => 'michel', 'attrs' => array('data-citation' => '"I love potatoes"', 'disabled' => 'disabled')),
    array('OPTGRP', 'Swedish Cars', 'disabled', array(
            array('volvo' => 'Volvo'),
            array('saab' => 'Saab')
        )
    ),
    array('OPTGRP', 'French Cars', '', array(
            array('peugeot' => 'Peugeot', 'attrs' => array('selected' => 'selected'))
        )
    )
);

$args = array('name' => 'myInput_02', 'label' => 'myLabel_02', 'options' => $options);
$input = new InputSelectField();
$input->prepare($args)->render()->display();