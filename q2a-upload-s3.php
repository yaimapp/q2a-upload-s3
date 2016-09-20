<?php

class q2a_upload_s3 {

	// option's value is requested but the option has not yet been set
	function option_default($option)
	{
		switch($option) {
			case 'q2a_upload_s3_enabled':
				return 1; // true
			default:
				return null;
		}
	}

	function allow_template($template)
	{
		return ($template != 'admin');
	}

	function admin_form(&$qa_content){

		// process the admin form if admin hit Save-Changes-button
		$ok = null;
		if (qa_clicked('q2a_upload_s3_save')) {
			qa_opt('q2a_upload_s3_enabled', (bool)qa_post_text('q2a_upload_s3_enabled')); // empty or 1
			$ok = qa_lang('admin/options_saved');
		}

		// form fields to display frontend for admin
		$fields = array();

		$fields[] = array(
			'type' => 'checkbox',
			'label' => qa_lang('q2a_upload_s3_lang/enable_plugin'),
			'tags' => 'NAME="q2a_upload_s3_enabled"',
			'value' => qa_opt('q2a_upload_s3_enabled'),
		);

		return array(
			'ok' => ($ok && !isset($error)) ? $ok : null,
			'fields' => $fields,
			'buttons' => array(
				array(
					'label' => qa_lang_html('main/save_button'),
					'tags' => 'name="q2a_upload_s3_enabled"',
				),
			),
		);
	}
}


/*
	Omit PHP closing tag to help avoid accidental output
*/
