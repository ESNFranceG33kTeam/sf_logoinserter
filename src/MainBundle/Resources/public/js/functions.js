function changeLogoLocation(src, orientation, logow, logoh){

	var width  = 960;
	var height = 720;
	
	var margin_left = "10px";
	if (orientation == "tr" || orientation == "tl") margin_left = (width - logow - 10) + "px";
	
	var margin_top = "10px";
	if (orientation == "br" || orientation == "bl") margin_top = (height - logoh - 10) + "px";
	
	html_img_logo = "<img id='img_logo' src='assets/img/logos/" + src + "' width='" +logow+ "' style='margin-left:" +margin_left+ ";margin-top:" +margin_top+";'>";
    $("#div_logo_display").empty();
    //$("#div_logo_display").html(html_img_logo);
}

/*
 * Start the upload & hide & show dives
 */
function startUpload(){
	$("#div_loading").show();
}

/* 
 * Stop the upload and hide & show dives
 */
function stopUpload(){
  $("#div_loading").hide();
  //$("#div_logo").hide();
  //$("#div_form").show();
}

function zipAll(){
	startUpload();
	$.ajax({
	   type: "GET",
	   url: "zip.php",
	   success: function(msg){
	     $("#span_info").hide();
	     $("#div_zip").html(msg);
	     
//	     var path = $("#path").html();
//	     download(path);
	   }
	 });
	stopUpload();	
}

function download(file)
{
 window.location=file;
}