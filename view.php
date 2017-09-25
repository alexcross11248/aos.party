<?php
	/*ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/

	if(!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
		exit;
	}
	$mapid = intval($_GET["id"]);
	
	$db = new SQLite3("/home/maps.db");
	$results = $db->query("select * from data where id=".$mapid.";");
	$data = $results->fetchArray(SQLITE3_ASSOC);
	$db->close();
	
	$sizestr = $data["size"]." B";
	if($data["size"]>1024) {
		$sizestr = round($data["size"]/1024,2)." KB";
	}
	if($data["size"]>1024*1024) {
		$sizestr = round($data["size"]/1024/1024,2)." MB";
	}
	
	if(file_exists($data["textfile"])) {
		$s = filesize($data["textfile"]);
		$sizestrtxt = $s." B";
		if($s>1024) {
			$sizestrtxt = round($s/1024,2)." KB";
		}
		if($s>1024*1024) {
			$sizestrtxt = round($s/1024/1024,2)." MB";
		}
	} else {
		$sizestrtxt = "?";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $data["name"]." - aos.party!";?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta property="og:site_name" content="aos.party!">
		<meta property="og:url" content="https://aos.party/">
		<meta property="og:title" content="Map: <?php echo $data["name"];?>">
		<meta property="og:image" content="https://aos.party/<?php echo $data["preview"]; ?>">
		<link rel="shortcut icon" href="/favicon.ico">
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css">
		<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
		<script>hljs.initHighlightingOnLoad();</script>
		<link href="zoom/advanced-zoom.min.css" rel="stylesheet">
		<link href="view.css" rel="stylesheet">
		<script>
			var images = ["<?php echo $data["isometric"]; ?>","<?php echo $data["topdown"]; ?>"];
			var image_index = 0;
			var nextimg = function(mod) {
				image_index = Math.min(Math.max(image_index+mod,0),images.length-1);
				document.getElementById("imageslide").src = images[image_index];
				document.getElementById("prev").style = (image_index==0)?"visibility: hidden;":"";
				document.getElementById("next").style = (image_index==images.length-1)?"visibility: hidden;":"";
			};
		</script>
	</head>
	<body onload="nextimg(0)">
		<center>
			<table border="0" width="900px" cellspacing="20" cellpadding="0">
				<tr>
					<td colspan="4"><a href="index.php"><img width="350px" src="aosparty.png" border="0" /></a></td>
					<!--<td valign="bottom"><span class="navbar">Search</span><span class="navbar">Explore</span></td>-->
				</tr>
				<tr>
					<td colspan="4"><hr width="100%"></td>
				</tr>
				<tr>
					<td><a href="javascript:nextimg(-1);"><img alt="Show previous image" align="right" src="arrowl.png" id="prev" border="0"/></a></td>
					<td class="imageview"><img width="400px" id="imageslide" src="<?php echo $data["isometric"]; ?>" border="0" data-zoom="zoom" /></td>
					<td><a href="javascript:nextimg(1);"><img alt="Show next image" src="arrowr.png" id="next" border="0" /></a></td>
					<td valign="top">
						<p><span class="header"><?php echo $data["name"]; ?></span></p>
						<p><span><i><?php if($data["version"]!="unknown") { echo "v".$data["version"]." "; } ?>by <?php echo $data["author"]; ?></i></span></p>
						<span><?php if($data["desc"]!="unknown") { echo $data["desc"]."<br /><br />"; } ?></span>
						<span class="headersmall">Downloads:</span>
						<table border="0" cellspacing="10" cellpadding="0">
							<tr height="40px">
								<td><a class="btn" href="dl.php?id=<?php echo $mapid; ?>&zip=1">ZIP</a></td>
								<td><a class="btn" href="dl.php?id=<?php echo $mapid; ?>&vxl=1">VXL</a></td>
								<td><a class="btn" href="dl.php?id=<?php echo $mapid; ?>&txt=1">TXT</a></td>
							</tr>
							<tr>
								<td><center><span>?</span></center></td>
								<td><center><span><?php echo $sizestr; ?></span></center></td>
								<td><center><span><?php echo $sizestrtxt; ?></span></center></td>
							</tr>
						</table>
						<br />
						<br />
						<br />
						<br />
						<br />
						<br />
						<?php if(!$data["textverified"]) { echo '<a class="btn" href="edit.php?id='.$mapid.'">Edit</a>'; } ?>
						<a class="btn" href="">Report an error</a>
					</td>
				</tr>
				<tr>
					<td colspan="3"><hr width="100%"></td>
					<td rowspan="2"></td>
				</tr>
				<tr><td colspan="3">
					<span class="header"><b>&gt;</b> <?php echo $data["name"]; ?>.txt:</span>
					<pre><code class="python"><?php echo file_exists($data["textfile"])?file_get_contents($data["textfile"]):"#This file is missing. Help us by [Edit]ing this entry :)";?></code></pre>
				</td></tr>
			</table>
		</center>
		<script src="zoom/advanced-zoom.min.js"></script>
	</body>
</html>