<?php
/*
 * Plugin Name: Visual constructor
 */
include_once('defines.php');
include_once('Core/Classes/Autoloader.php');
VconstAutoloader::register();

include_once('functions.php');
include_once('Core/functions.php');

include_once('Classes/VisualConstructorPlugin.php');
$visual_constructor = new VisualConstructorPlugin();
