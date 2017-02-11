<?php
    include('class.uploader.php');
	include ('OfxParser/Ofx.php');
	include ('OfxParser/Parser.php');
	
    $uploader = new Uploader();
    $data = $uploader->upload($_FILES['files'], array(
        'limit' => 1, //Maximum Limit of files. {null, Number}
        'maxSize' => 1, //Maximum Size of files {null, Number(in MB's)}
        'extensions' => array('ofx'), //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
        'required' => true, //Minimum one file is required for upload {Boolean}
        'uploadDir' => 'uploads/', //Upload directory {String}
        'title' => array(md5(uniqid(rand(), true))), //New file name {null, String, Array} *please read documentation in README.md
        'removeFiles' => true, //Enable file exclusion {Boolean(extra for jQuery.filer), String($_POST field name containing json data with file names)}
        'replace' => true, //Replace the file if it already exists  {Boolean}
        'perms' => null, //Uploaded file permisions {null, Number}
        'onCheck' => null, //A callback function name to be called by checking a file for errors (must return an array) | ($file) | Callback
        'onError' => null, //A callback function name to be called if an error occured (must return an array) | ($errors, $file) | Callback
        'onSuccess' => null, //A callback function name to be called if all files were successfully uploaded | ($files, $metas) | Callback
        'onUpload' => null, //A callback function name to be called if all files were successfully uploaded (must return an array) | ($file) | Callback
        'onComplete' => null, //A callback function name to be called when upload is complete | ($file) | Callback
        'onRemove' => null //A callback function name to be called by removing files (must return an array) | ($removed_files) | Callback
    ));
	
	
    if($data['isComplete']){
        $info = $data['data'];
		
		$ofxParser = new OfxParser\Parser();
        $ofx = $ofxParser->loadFromFile($info['files'][0]);
		
        $account = reset($ofx->bankAccounts);
		
		echo json_encode($account);
		
		unlink($info['files'][0]);
    }

    if($data['hasErrors']){
        $errors = $data['errors'];
        print_r($errors);
    }
?>
