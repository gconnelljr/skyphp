<?php
	/**
	*	USING UPLOADER
	*	URL/Form Action : /media/upload/table_name(ex. ctn_order)
	*   DATA : $_FILES Array and $_POST Array
	*		- $_FILES (Can upload Multiple Files, but can cause issues if filename is specified)
	*		- $_POST = [
	*			'folder' => 'full folder path that is needed', // REQUIRED
	*			'redirectURL' => 'Current page that you're on (usually $this->uri)', // OPTIONAL
	*			'filename' => 'desired file name', // OPTIONAL (Use array of file name with the same name attribute as desired file name appended with '_cust')
	*			'override' => '0 or 1', // OPTIONAL
	*			'save' => '0 or 1', // OPTIONAL
	*			'ide' => 'grewniuj' // REQUIRED if using save method
	*		]
	*   RETURN : JSON Encoded String 
	*/
	use \Sky\skyMedia;
	if($_POST && count($_POST) > 0 && $_FILES){
		$data = [
			'folder' => $_POST['folder'],
			'files' => $_FILES,
			'filename' => $_POST['filename'],
			'override' => $_POST['override']
		];
		$upload = skyMedia::fileUpload($data); // Already encoded to json
		$url = $_POST['redirectURL'] ? $_POST['redirectURL'] : $_SERVER['HTTP_REFERER'];
		
		if ($_POST->is_ajax_request || $_POST['is_ajax_request']){
		    exit_json($upload);
		} elseif($_POST['save']) {
			// Find IDE in $_POST array and save object
			$namespace = '\\Crave\\Model\\';
			$model_name = $namespace .  IDE;
			$model = new $model_name($_POST['ide']);
			// Name function that handles media items in model saveMediaItems()
			if(method_exists($model, "saveMediaItems")){
				$model->saveMediaItems($upload, $_POST);
			}
			redirect($url);
		} else {
			$qs = (strpos($_SERVER['HTTP_REFERER'], "?") !== FALSE || strpos($_POST['redirectURL'], "?")) !== FALSE ? '&return='.rawurlencode($upload) : '?return='.rawurlencode($upload);
			redirect($url . $qs);
		}
	} else {
		// Handle case where files were not uploaded
		throw new \Exception('No data/files were sent.  Could not upload.');
	}
?>