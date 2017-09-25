<!DOCTYPE html>
<html>
	<head>
		<title>aos.party! - The Ace of Spades map repository</title>
		<meta name="description" content="More than 1000+ maps for download, as .zip or directly as a .vxl file. We also collect information about maps, such as author, desc, ..." />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta property="og:site_name" content="aos.party!">
		<meta property="og:url" content="https://aos.party/">
		<meta property="og:title" content="Search for Ace of Spades maps quickly">
		<meta property="og:image" content="https://aos.party/aosparty_small.png">
		<link rel="shortcut icon" href="/favicon.ico">
		<link href="index.css" rel="stylesheet">
		<script type="text/javascript">
			var callAjax = function(url, callback, container, container2) {
				var xmlhttp;
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function(){
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
						callback(xmlhttp.responseText,container,container2);
					}
				}
				xmlhttp.open("GET", url, true);
				xmlhttp.send();
			};
			
			var last_search = 0;
			var onSearch = function() {
				setTimeout(search,500);
			};
			
			var author = function(author) {
				document.getElementById("search").value = "author:"+author;
				s(1);
			};
			
			var getTextWidth = function(text, font) {
				var canvas = getTextWidth.canvas || (getTextWidth.canvas = document.createElement("canvas"));
				var context = canvas.getContext("2d");
				context.font = font;
				return context.measureText(text).width;
			};
				
			var query = function(response,container,container2) {
				var j = JSON.parse(response);
				if(container2!=0) {
					if(j.total==j.entries.length) {
						document.getElementById(container2).innerHTML = '<p><span class="headersmall">Results: '+j.total+'</span></p>';
					} else {
						var pages = "Page ";
						for(var k=0;k<j.total/24;k++) {
							pages += '<a href="javascript:s('+(k+1)+')">'+(k+1)+'</a> ';
						}
						document.getElementById(container2).innerHTML = '<p><span class="headersmall">Results: '+j.total+', Shown: '+j.entries.length+'</span></p><p><span class="headersmall">'+pages+'</span></p>';
					}
				}
				var html = '<table border="0" cellspacing="0" cellpadding="0"><tr>';
				var cnt = 0;
				for(var entry of j.entries) {
					var n = entry["name"];
					var a = entry["author"];
					if(getTextWidth(n,"16px Verdana") > 170) {
						while(getTextWidth(n+"...","16px Verdana") > 170) {	
							n = n.substring(0,n.length-1);
						}
						n += "...";
					}
					if(getTextWidth("by "+a,"italic 12px Verdana") > 170) {
						while(getTextWidth("by "+a+"...","italic 12px Verdana") > 170) {
							a = a.substring(0,a.length-1);
						}
						a += "...";
					}
					html += '<td><a href="view.php?id='+entry["id"]+'"><img src="'+entry["preview"]+'" border="0" /></a><br />';
					html += '<span class="list_mapname"><a href="view.php?id='+entry["id"]+'">'+n+'</a></span><br />';
					html += '<span class="list_mapcreator">by <a href="javascript:author(\''+a+'\')">'+a+'</a></span></td>';
					cnt++;
					if((cnt%6)==0) {
						html += '</tr><tr><td colspan="6" height="10px"><hr width="100%"></td></tr><tr>';
					}
				}
				html += "</tr></table>";
				document.getElementById(container).innerHTML = html;
			};
			
			var search = function() {
				if(Date.now()-last_search>450) {
					callAjax("search.php?q="+document.getElementById("search").value,query,"results","resultsamount");
					last_search = Date.now();
				}
			};
			
			var s = function(page) {
				callAjax("search.php?q="+document.getElementById("search").value+"&page="+page,query,"results","resultsamount");
			};
			
			var load = function() {
				var e = document.getElementById("search");
				e.oninput = onSearch;
				e.onpropertychange = e.oninput;
				
				callAjax("search.php?mapsofweek=1&q=a",query,"mapsofweek",0);
				callAjax("search.php?lastadded=1&q=a",query,"lastmaps",0);
			};
		</script>
	</head>
	<body onload="load()" bgcolor="white">
		<center>
			<table border="0" cellspacing="20px"><tr><td>
				<table width="1050px" border="0">
					<tr><td><img width="350px" src="aosparty.png" border="0" /></td></tr>
					<tr><td><hr width="100%"></td></tr>
				</table>
			</td></tr><tr><td>
				<table width="1050px" border="0">
					<tr><td><span class="header red"><b>&gt;</b> Search repo</span><p><input type="text" placeholder="type here" id="search" /></p><div id="resultsamount"></div></td></tr>
				</table>
			</td></tr><tr><td>
				<div id="results">&nbsp;</div>
			</td></tr><tr><td>
				<table width="1050px" border="0">
					<tr><td><span class="header"><b>&gt;</b> Maps of the week</span></td></tr>
				</table>
			</td></tr><tr><td>
				<div id="mapsofweek">&nbsp;</div>
			</td></tr><tr><td>
				<table width="1050px" border="0">
					<tr><td><span class="header"><b>&gt;</b> Last added</span></td></tr>
				</table>
			</td></tr><tr><td>
				<div id="lastmaps">&nbsp;</div>
			</td></tr></table>
		</center>
	</body>
</html>