<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	require "ZipStream/ZipStream.php";

	if(!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
		exit;
	}
	$mapid = intval($_GET["id"]);
	
	$db = new SQLite3("/home/maps.db");
	$results = $db->query("select * from data where id=".$mapid.";");
	$data = $results->fetchArray(SQLITE3_ASSOC);
	
	header("Content-Type: application/octet-stream");
	header("Content-Transfer-Encoding: Binary");
	
	$txt_gen = "name = '".$data["name"]."'\r\n";
	if($data["author"]!="unknown") {
		$txt_gen .= "author = '".$data["author"]."'\r\n";
	}
	if($data["desc"]!="unknown") {
		$txt_gen .= "description = '".$data["desc"]."'\r\n";
	}
	if($data["version"]!="unknown") {
		$txt_gen .= "version = '".$data["version"]."'\r\n";
	}
	
	if(isset($_GET["vxl"])) {
		header("Content-disposition: attachment; filename=\"".$data["name"].".vxl\""); 
		$fh = xzopen($data["filename"],"r");
		xzpassthru($fh);
		xzclose($fh);
	} else {
		if(isset($_GET["txt"])) {
			header("Content-disposition: attachment; filename=\"".$data["name"].".txt\""); 
			if(file_exists($data["textfile"])) {
				readfile($data["textfile"]);
			} else {
				echo $txt_gen;
			}
		} else {
			if(isset($_GET["zip"])) {
				header("Content-disposition: attachment; filename=\"".$data["name"].".zip\""); 
				$zip = new ZipStream\ZipStream($data["name"].".zip");
				
				$fh = xzopen($data["filename"],"r");
				$zip->addFileFromStream($data["name"].".vxl",$fh);
				xzclose($fh);
				
				if(file_exists($data["textfile"])) {
					$zip->addFileFromPath($data["name"].".txt",$data["textfile"]);
				} else {
					$zip->addFile($data["name"].".txt",$txt_gen);
				}
				$zip->addFile("NOTICE.txt","Downloaded from https://aos.party/");
				$zip->finish();
			}
		}
	}
	
	$db->close();
?>
