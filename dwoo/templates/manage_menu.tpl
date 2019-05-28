<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>{t}Manage Boards{/t}</title>
{loop $styles}
	<link rel="{if $ neq %KU_DEFAULTSTYLE}alternate {/if}stylesheet" type="text/css" href="{%KU_WEBPATH}/css/site_{$}.css" />
	<link rel="{if $ neq %KU_DEFAULTSTYLE}alternate {/if}stylesheet" type="text/css" href="{%KU_WEBPATH}/css/sitemenu_{$}.css" />
{/loop}
<link rel="shortcut icon" href="{%KU_WEBPATH}/favicon.ico" />
{literal}
<script type="text/javascript">
function toggle(button, area) {
	var tog=document.getElementById(area);
	if(tog.style.display)	{
		tog.style.display="";
	} else {
		tog.style.display="none";
	}
	button.innerHTML=(tog.style.display)?'+':'&minus;';
	createCookie('nav_show_'+area, tog.style.display?'0':'1', 365);
}
</script>
{/literal}
<base target="manage_main" />
</head>
<body>
<h1>{t}Manage Boards{/t}</h1>
<ul>
	{$links}
</ul>
</body>
</html>