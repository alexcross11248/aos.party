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
	
	if($data["textverified"]>0) {
		//exit;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $data["name"]." - aos.party!";?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="shortcut icon" href="/favicon.ico">
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css">
		<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
		<script>hljs.initHighlightingOnLoad();</script>
		<link href="view.css" rel="stylesheet">
		<style>
			input[type="text"] {
			  margin: 0;
			  font-family: sans-serif;
			  font-size: 18px;
			  appearance: none;
			  box-shadow: none;
			  border-radius: none;
			  padding: 10px;
			  border: solid 1px #dcdcdc;
			  transition: box-shadow 0.3s, border 0.3s;
			}
			input[type="text"]:focus {
			  outline: none;
			  border: solid 1px #707070;
			  box-shadow: 0 0 5px 1px #005096;
			}
		</style>
	</head>
	<body onload="nextimg(0)">
		<center>
			<table border="0" width="900px" cellspacing="20" cellpadding="0">
				<tr>
					<td colspan="2"><a href="index.php"><img width="350px" src="aosparty.png" border="0" /></a></td>
					<!--<td valign="bottom"><span class="navbar">Search</span><span class="navbar">Explore</span></td>-->
				</tr>
				<tr>
					<td colspan="2"><hr width="100%"></td>
				</tr>
				<tr>
					<td><img src="<?php echo $data["preview"]; ?>" border="1"/></td>
					<td width="100%" valign="top">
						<p><span class="header"><b>&gt;</b> Editing <?php echo $data["name"]; ?></span></p>
					</td>
				</tr>
				<tr>
					<td colspan="2"><hr width="100%"></td>
				</tr>
				<tr>
					<td colspan="2">
						<p>
							<span style="font-size: 18px">Author: </span><input type="text" placeholder="e.g. Influx" id="author" value="<?php echo ($data["author"]!="unknown")?$data["author"]:""; ?>" style="width: 250px;" />
							&nbsp;
							<span style="font-size: 18px">Version: </span><input type="text" placeholder="e.g. 1.0" value="<?php echo ($data["version"]!="unknown")?$data["version"]:""; ?>" id="version" style="width: 80px;" />
						</p>
						<p>
							<span style="font-size: 18px">Description: </span><br />
							<textarea rows="6" cols="65"><?php echo $data["desc"]!="unknown"?$data["desc"]:""; ?></textarea>
						</p>
						<br />
						<a class="btn" href="">Save!</a>
						<a class="btn" href="view.php?id=<?php echo $mapid; ?>">Abort</a>
					</td>
				</tr>
			</table>
		</center>
		<script src="zoom/advanced-zoom.min.js"></script>
	</body>
</html>