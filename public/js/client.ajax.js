	function chooseimage(image,id) {
  $('#response2').html('<br />');
  $('#cancelnote').click(function(){
	$('#tern').imgAreaSelect({ hide: true });
	$('#noteform').hide();
  });
  $('#image-placeholder').html('<img src="assets/'+image+'" id="tern" alt="Project Image" />');
  $('#response3').html('<a href="#" onclick="deleteasset('+id+')"> <img src="images/delete.png" /> <span>Delete Asset</span></a> <a href="assets/'+image+'"> <img src="images/box.png" /> <span>Download Asset</span></a>');
  $('#NoteimgID').val(id);
  $(".note").remove(); 
  $(".notep").remove(); 
$.post("includes/ajax/loadnotes.php", { asset: id, },
  function(data){
    $('#response').html(data);
  });
	}
		
	$(window).load(function () { 
		$('#cancelnote').click(function(){
			$('#tern').imgAreaSelect({ hide: true });
			$('#noteform').hide();
			$('#note').hide();
		});
		$('#addnotelink').click(function(){
			$('#tern').imgAreaSelect({ onSelectChange: showaddnote, x1: 120, y1: 90, x2: 280, y2: 210 });
			return false;
		});
	});
		
	function showaddnote (img, area) {
		imgOffset = $(img).offset();
		form_left  = parseInt(imgOffset.left) + parseInt(area.x1);
		form_top   = parseInt(imgOffset.top) + parseInt(area.y1) + parseInt(area.height)+5;
		$('#noteform').css({ left: form_left + 'px', top: form_top + 'px'});
		$('#noteform').show();
		$('#noteform').css("z-index", 10000);
		$('#NoteX1').val(area.x1);
		$('#NoteY1').val(area.y1);
		$('#NoteHeight').val(area.height);
		$('#NoteWidth').val(area.width);
	}
	
	function addnote() {
		var imgID = $('#NoteimgID').val();
		var x1 = $('#NoteX1').val();
		var y1 = $('#NoteY1').val();
		var height = $('#NoteHeight').val();
		var width = $('#NoteWidth').val();
		var note = $('#NoteNote').val();
		var by = $('#NoteBy').val();
$.post("includes/ajax/addnote.php", { id: imgID, x1: x1, y1: y1, height: height, width: width, note: note, by: by, },
  function(data){
    $('#response').html(data);
  });
	$('#tern').imgAreaSelect({ hide: true });
	$('#noteform').hide();
$(".note").remove(); 
$(".notep").remove(); 
$('#note').hide();
	}
	
	function deletenote(image,imgid,noteid) {
$(".note").remove(); 
$(".notep").remove(); 
$('#response2').html('<br />');
$.post("includes/ajax/deletenote.php", { id: noteid,},
  function(data){
    $('#response2').html(data);
  });
	chooseimage(image,imgid);
	}
	
	function deleteasset(assetid) {
$(".note").remove(); 
$(".notep").remove(); 
$('#note').hide();
$('#response').html('<br />');
$('#response3').html('');
$(".assetnum"+assetid).remove(); 

$.post("includes/ajax/deleteasset.php", { id: assetid,},
  function(data){
    $('#response2').html(data);
  });
  $('#image-placeholder').html('<p><br />Click on the images to the right to display them in this viewing<br />pane. Additionally, you can tag images with \'Notes\'.</p>');
	}
	
	function choosefile(asset,id) {
$(".note").remove(); 
$(".notep").remove(); 
$('#note').hide();
$('#response2').html('<br />');
$('#response').html('<br />');
  $('#response3').html('<a href="#" onclick="deleteasset('+id+')"> <img src="images/delete.png" /> <span>Delete Asset</span></a> <a href="assets/'+asset+'"> <img src="images/box.png" /> <span>Download Asset</span></a>');
  $('#image-placeholder').html('<p><br />To download <strong>'+asset+'</strong>, click on the \'Download Asset\' link below.</p>');
	}