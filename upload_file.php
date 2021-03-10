<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

$upload_dir = "./uploads/"; //path to upload directory
$upload_dir_url = "./uploads/"; //url pointing to upload directory

//make sure uploaded file is not empty
if(!isset($_FILES['fds_data']) || !is_uploaded_file($_FILES['fds_data']['tmp_name'])){
	//generate an error json response
	$response = json_encode(
					array( 
						'type'=>'error', 
						'msg'=>'File is Missing!'
					)
				);
				
	print $response;
	exit;
}

// NOTE - when running this php within nginx I found that the file upload was limited to 1MB. I had to
// edit the file /etc/nginx/nginx.conf and add the line "client_max_body_size 30M;" under the Basic Settings section
// to get the server to accept larger files. Note in index.php I have limited the upload size to 20MB anyway.

if(move_uploaded_file($_FILES['fds_data']['tmp_name'], $upload_dir . basename($_FILES['fds_data']['name']))){
	// create a unique folder name...
	$folderdate = 'job_' . date('YmdHis');
	$folder_number = 1;
	while (is_dir($upload_dir . $folderdate)) {
		$folderdate = 'job_' . date('YmdHis') . '_' . $folder_number;
		$folder_number++;
	}
	// create a folder with this name
	mkdir($upload_dir . $folderdate);
	
	// NOTE! it is potentially possible that another folder might be created between the name being defined and the folder being created
	// This should be done a different way, especially if the server is very busy.
	
	// Create a list of the files to process. For now, this prevents the uploaded filename being passed straight to the commandline
	// but in the future, might allow you to upload multiple files to be wrapped in one disk image
	$filelist = fopen($upload_dir . $folderdate . "/filelist.txt", "w") or die("Unable to open file!");
	fwrite($filelist, $upload_dir . '/' . $_FILES['fds_data']['name']);
	fclose($filelist);
	
	// take the uploaded file and wrap it in a hfs iso file... Images mounting name is the uploaded file name, and the disk image file is named for the unique folder name.
	//$output = shell_exec('genisoimage -hfs -hfs-unlock -probe -V "'. $_FILES['fds_data']['name'] . '" -o ' . $upload_dir . $folderdate . '/' . $folderdate . '.dsk ' . $upload_dir . $_FILES['fds_data']['name'] );
	$output = shell_exec('genisoimage -hfs -hfs-unlock -probe -V "'. $_FILES['fds_data']['name'] . '" -o ' . $upload_dir . $folderdate . '/' . $folderdate . '.dsk -path-list ' . $upload_dir . $folderdate . "/filelist.txt" );
	
	// delete the uploaded file (I'm not storing everything!)
	unlink( './uploads/' . $_FILES['fds_data']['name'] );
	
	// the wrapped disk images should be removed with a cron scheduled clearout. For example :
	// sudo find /sharedfolders/macimagefolder/uploads/ -maxdepth 1 -mmin +20 -type d -exec rm -r {} \;
	// but using the correct path to uploads for your machine
	
	echo $output;
	$results = 1;
}
	
//output success response
if($results){
	// the text / link for the download offered to the user
	print json_encode(array('type'=>'success', 'msg'=>'Wrapping Complete!<br/>Click here to download<br/>your disk image of...<br/><a href="' . $upload_dir_url . $folderdate . '/' . $folderdate . '.dsk" download> ' . $_FILES['fds_data']['name'] . '</a>'));
	exit;
}else{
	print json_encode(array('type'=>'success', 'msg'=>'File not uploaded!'));
	exit;
}
