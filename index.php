<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>HFS Disk Image Wrapper Web Interface</title>
<style>
h1{
	text-align:center;
}
div.upload-box{
    width: 300px;
    margin: 0 auto;
    text-align: center;
}
div.upload-box label{
	background: #eaf9ff;
    text-align: center;
    padding: 20px;
    box-sizing: border-box;
    display: inline-block;
    border: 2px dashed #abe5ff;
    cursor: pointer;
    color: #3dc2fd;
    border-radius: 4px;
    width: 300px;
    overflow: auto;
}
div.upload-box input[type=file]{
	    display: none;
}
.server-results img{
	width: 100%;
    height: auto;
}

</style>
</head>
<body>
<h1>HFS Disk Image Wrapper Web Interface</h1>
After uploading your file, there will be no indication of activity until it has finished processing. Leave the window open, and once it is done you will be offered a download link for the results.<br>It is recommended that you refresh the page between each upload or the last download link will still be there and you wont know that your processing has finished.<br><br>
<p id="status1"></p>
<div class="upload-box">
	<label for="fds-file">Upload file</label>
	<input type="file" name="fds-file" id="fds-file" accept="text/*" />
	<div id="server-results"></div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
var ajax_url = "upload_file.php";
var max_file_size = 20971520;


$("#fds-file").change(function(){
	if(window.File && window.FileReader && window.FileList && window.Blob){
		if($(this).val()){
			var oFile = $(this)[0].files[0];
			var fsize = oFile.size; //get file size
			var ftype = oFile.type; // get file type
			
			if(fsize > max_file_size){
				alert("File size can not be more than (" + max_file_size +") 20MB" );
				return false;
			}
			
			document.getElementById("status1").innerHTML = "Processing...";			
			
			var mdata = new FormData();
			mdata.append('fds_data', oFile);
				
			jQuery.ajax({
				type: "POST", // HTTP method POST
				processData: false,
				contentType: false,
				url: ajax_url, //Where to make Ajax call
				data: mdata, //Form variables
				dataType: 'json',
				success:function(response){
					if(response.type == 'success'){
						$("#server-results").html('<div class="success">' + response.msg + '</div>');
					}else{
						$("#server-results").html('<div class="error">' + response.msg + '</div>');
					}
				}
			});
		}
	}else{
		alert("Can't upload! Your browser does not support File API!</div>");
		return false;
	}
	document.getElementById("status1").innerHTML = "Ready.";
});	


</script>
</body>
</html>
