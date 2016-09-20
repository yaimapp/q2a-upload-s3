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

if (!defined('QA_BLOBS_DIRECTORY')) {
	define('QA_BLOBS_DIRECTORY', QA_BASE_DIR .'qa-uploads');
}

// language file
qa_register_plugin_phrases('q2a-upload-s3-lang-*.php', 'q2a_upload_s3_lang');

// admin
qa_register_plugin_module('module', 'q2a-upload-s3.php', 'q2a_upload_s3', 'Upload S3');
// // override
// qa_register_plugin_overrides('qa-upload-s3-overrides.php');
