/*emo.js*/
function popBox(){
	//get the wrapper element
	var w = document.getElementById('wrapper');
	
	//gray out the screen
	var grayout = document.createElement('div');
	grayout.id = 'grayout';
	grayout.style.backgroundColor = "#999";
	grayout.style.opacity = "0.5";
	grayout.style.position = "absolute";
	grayout.style.width = $(document).width() + 'px';
	grayout.style.height = $(document).height() + 'px';
	$('#wrapper').before(grayout);
	
	//create div on the fly. center it in window.
	var mynewdiv = document.createElement('div');
	mynewdiv.id = 'popdiv';
	mynewdiv.style.width = '475px';
	mynewdiv.style.height = '375px';
	mynewdiv.style.backgroundColor = "#fff";
	mynewdiv.style.border = "2px solid #333";
	
	//add new div to wrapper div
	w.appendChild(mynewdiv);
	mynewdiv.style.position = 'absolute';
	mynewdiv.style.top = (window.pageYOffset + 50) +'px'; //position at top of current area on page
	mynewdiv.style.padding = '20px';
    mynewdiv.style.marginLeft = '242px';
	return mynewdiv;
}

function insertForm(div){
	div.className = 'checkerMsgBox';
	div.style.border = "2px solid #999933";
	//insert cloud background
	var go = document.getElementById('grayout');
	go.style.background= 'url("images/clouds_bg.gif") no-repeat center #fff';
	go.style.opacity = "1";
	
	var content = '<p class="close_link"><a href="javascript:closeBox(\'popdiv\')"><img src="images/close_btn.gif"/></a></p>';
	content+= '<h3>Take a load off</h3>'; 
	content+= '<form id="form1" name="form1" method="post" action="" onsubmit="sendCheckerMsg();return false;">';
	content+= '<textarea name="tell" id="tell" cols="45" rows="5"></textarea>';
	content+= '<input type="submit" name="btn" id="btn" value="Check it" /></form>';
	div.innerHTML  = content;
	
}

function insertConfirm(div){
	div.style.border= "2px solid #008AB8";
	var content = makeCloseBtn();
	content += "<h3>Ready to hear it?</h3>";
	content+= "<p>We're about to show you someone's emotional baggage. Just want to make sure you're up for it.</p>"; 
	content+= '<div class="confirm_links"><a href="javascript:closeBox(\'popdiv\')" class="confirm_no">No thanks</a> ';
	content+= '<a href="showCheckerMsg.php" class="confirm_yes">Bring it on</a></div>';
	div.innerHTML = content;
}

function popCheckIt(){
	var box = popBox();
	insertForm(box);
}

function popCarryIt(){
	var box = popBox();
	insertConfirm(box);
}

function closeBox(boxname){
	var box = document.getElementById(boxname);
	var w = document.getElementById('wrapper');
	w.removeChild(box);
	//remove gray-out
	$('#grayout').remove();
	
}

function songSearch(){
    var str = document.getElementById('song').value;
	var cid = document.getElementById("checker_id").value;
    document.getElementById("songbox").innerHTML = '<img src="images/loading29.gif" class="song-loading"/>';
    $.ajax({
       type: "POST",
       url: "ajax/songsearch.php",
       data: "song_search="+str+'&checker_id='+cid,
       success: function(msg){
         document.getElementById("songbox").innerHTML = msg;
       }
    });
	return false;
}

function sendSong(queryStr){
	var box = popBox();
    var btn = makeCloseBtn();
	box.innerHTML =btn+'<p>Preparing song ...</p>';
    
	$.ajax({
       type: "POST",
       url: "ajax/sendsong.php",
       data: queryStr,
       success: function(msg){
        var content = makeCloseBtn();
        content += msg;
		box.innerHTML = content;
		box.className = 'takerMsgBox';
       }
    });
}

function sendCheckerMsg(){
	var str = document.getElementById('tell').value;
	$.ajax({
       type: "POST",
       url: "ajax/bagcheck.php",
       data: "tell="+str,
       success: function(msg){
		 document.getElementById('popdiv').innerHTML = msg;
       }
    });
}
function makeCloseBtn(){
	var html = '<p class="close_link"><a href="javascript:closeBox(\'popdiv\')"><img src="images/close_btn.gif"/></a></p>';
	return html;
}