<?php
if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

// s3用のディレクトリ名取得
function s3_get_blob_directory($blobid)
{
	return 's3://'. qa_opt(US3_S3_BUCKET) . '/' . substr(str_pad($blobid, 20, '0', STR_PAD_LEFT), 0, 3);
}

// s3用のファイル名取得
function s3_get_blob_filename($blobid, $format)
{
	return s3_get_blob_directory($blobid).'/'.$blobid.'.'.preg_replace('/[^A-Za-z0-9]/', '', $format);
}

// STREAM WRAPPERを登録できたか？
function is_s3_enabled()
{
	if (qa_opt(US3_ENABLED)
		&& !empty(qa_opt(US3_AWS_ID))
		&& !empty(qa_opt(US3_AWS_SECRET))
		&& !empty(qa_opt(US3_S3_REGION))
		&& !empty(qa_opt(US3_S3_BUCKET)) ) {

		require_once QA_PLUGIN_DIR . 'q2a-upload-s3/vendor/autoload.php';

		try {
			$s3 = Aws\S3\S3Client::factory(array(
				'key'    => qa_opt(US3_AWS_ID),
				'secret' => qa_opt(US3_AWS_SECRET),
				'region' => qa_opt(US3_S3_REGION),
			));

			$s3->registerStreamWrapper();

		} catch (S3Exception $e) {
			error_log($e->getMessage());
			return false;
		}
		return true;
	}
	return false;
}

function qa_write_blob_file($blobid, $content, $format)
{
	$written=false;

	if (is_s3_enabled()) {

		try {
			date_default_timezone_set("Asia/Tokyo");
			// $directory = s3_get_blob_directory($blobid);

			$filename = s3_get_blob_filename($blobid, $format);

			$file=fopen($filename, 'xb');
			if (is_resource($file)) {
				if (fwrite($file, $content)>=strlen($content))
					$written=true;

				fclose($file);

				if (!$written)
					unlink($filename);
			}

		} catch (S3Exception $e) {
			error_log($e->getMessage());
			$written = false;
		}
		$s3 = null;
	}

	if (!$written) {
		// まだ書き込まれていない場合はサーバーのディレクトリに保存
		$directory = qa_get_blob_directory($blobid);
		if (is_dir($directory) || mkdir($directory, fileperms(rtrim(QA_BLOBS_DIRECTORY, '/')) & 0777)) {
			$filename = qa_get_blob_filename($blobid, $format);

			$file=fopen($filename, 'xb');
			if (is_resource($file)) {
				if (fwrite($file, $content)>=strlen($content))
					$written=true;

				fclose($file);

				if (!$written)
					unlink($filename);
			}
		}
	}

	return $written;
}

function qa_read_blob_file($blobid, $format)
{
	$contents = null;
	if (is_s3_enabled()) {
		try {
			$filename = s3_get_blob_filename($blobid, $format);
			if (is_readable($filename)) {
				$contents = file_get_contents($filename);
			}
		} catch (S3Exception $e) {
			error_log($e->getMessage());
			$contents = null;
		}
	}

	if (isset($contents)) {
		return $contents;
	} else {
		$filename = qa_get_blob_filename($blobid, $format);
		if (is_readable($filename))
			return file_get_contents($filename);
		else
			return null;
	}
}
