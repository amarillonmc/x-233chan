function toggle(button, area) {
	var tog=document.getElementById(area);
	if(tog.style.display)    {
		tog.style.display="";
	}    else {
		tog.style.display="none";
	}
	button.innerHTML=(tog.style.display)?'+':'&minus;';
	set_cookie('nav_show_'+area, tog.style.display?'0':'1', 30);
}
function removeframes() {
	var boardlinks = document.getElementsByTagName("a");
	for(var i=0;i<boardlinks.length;i++) if(boardlinks[i].className == "boardlink") boardlinks[i].target = "_top";
	
	document.getElementById("removeframes").innerHTML = 'Frames removed.';
	
	return false;
}
function reloadmain() {
	if (parent.main) {
		parent.main.location.reload();
	}
}