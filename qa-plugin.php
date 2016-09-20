<?php

/*
	Plugin Name: Upload S3 Plugin
	Plugin URI:
	Plugin Description: Image file save Amzaon S3.
	Plugin Version: 1.0
	Plugin Date: 2016-09-20
	Plugin Author: 38qa.net
	Plugin Author URI: http://38qa.net/
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.7
	Plugin Update Check URI:
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

// // admin
// qa_register_plugin_module('module', 'qa-upload-s3.php', 'qa_upload_s3', 'Upload S3');
// // override
// qa_register_plugin_overrides('qa-upload-s3-overrides.php');
