<?php
	error_reporting( E_ALL );
	ini_set('display_errors', 1);
	require_once('config/settings.inc.php');
	$dsn = "mysql:host=mariadb55.websupport.sk;port=3310;unix_socket=/tmp/mariadb55.sock;dbname="._DB_NAME_;
	try{
	  $db = new PDO($dsn, _DB_USER_, _DB_PASSWD_);
	  $db->exec("SET CHARACTER SET utf8");
	} catch (Exception $e) {
		echo "Failed: " . $e->getMessage();
	    $db->rollBack();
	}
	$queryString = "SELECT pa.id_product_attribute,(
					SELECT al.name FROM ps_product_attribute_combination pac
					LEFT JOIN ps_attribute a ON a.id_attribute = pac.id_attribute
					LEFT JOIN ps_attribute_lang al ON al.id_attribute = pac.id_attribute 
					where a.id_attribute_group = 10 AND pac.id_product_attribute = pa.id_product_attribute) as gramaz, 
					(SELECT al.name FROM ps_product_attribute_combination pac
					LEFT JOIN ps_attribute a ON a.id_attribute = pac.id_attribute
					LEFT JOIN ps_attribute_lang al ON al.id_attribute = pac.id_attribute 
					where a.id_attribute_group = 11 AND pac.id_product_attribute = pa.id_product_attribute) as dlzka, 
					(SELECT al.name FROM ps_product_attribute_combination pac
					LEFT JOIN ps_attribute a ON a.id_attribute = pac.id_attribute
					LEFT JOIN ps_attribute_lang al ON al.id_attribute = pac.id_attribute 
					where a.id_attribute_group = 12 AND pac.id_product_attribute = pa.id_product_attribute) as farba
					FROM ps_product_attribute pa 
					WHERE pa.id_product = 14"; // 14 - id specialneho produktu
	$sqlQueryContent = $queryString;
	$resContent = $db->prepare($sqlQueryContent);
	$resContent->execute();
	$percentage = 1.2;
	while ($row = $resContent->fetch(PDO::FETCH_OBJ)){
		$attributePrice = 0;
		if($row->dlzka == 100){
			$attributePrice = round(((1.8 * $row->gramaz) / $percentage), 5);
			//echo $row->gramaz.", ".$row->dlzka.", ".$attributePrice.", ".$row->id_product_attribute."<br>";
		}elseif($row->dlzka == 200){
			$attributePrice = round(((2 * $row->gramaz) / $percentage), 5);
			//echo $row->gramaz.", ".$row->dlzka.", ".$attributePrice.", ".$row->id_product_attribute."<br>";
		}
		$update = $db->prepare("UPDATE ps_product_attribute SET price = ? where id_product_attribute = ?");
    	$updateStatus = $update->execute(array($attributePrice,$row->id_product_attribute));
    	$update = $db->prepare("UPDATE ps_product_attribute_shop SET price = ? where id_product_attribute = ?");
    	$updateStatus = $update->execute(array($attributePrice,$row->id_product_attribute));

		$haveColor = false;
		$colorId = 0;
		if($row->farba == "Čierna"){
			$haveColor = true;
			$colorId = 49;
		}elseif($row->farba == "Tmavo hnedá"){
			$haveColor = true;
			$colorId = 50;
		}elseif($row->farba == "Stredne hnedá"){
			$haveColor = true;
			$colorId = 51;
		}elseif($row->farba == "Hnedá"){
			$haveColor = true;
			$colorId = 52;
		}elseif($row->farba == "Svetlo hnedá"){
			$haveColor = true;
			$colorId = 53;
		}elseif($row->farba == "Zlato hnedá"){
			$haveColor = true;
			$colorId = 54;	
		}elseif($row->farba == "Svetlo zlatá blond"){
			$haveColor = true;
			$colorId = 55;
		}elseif($row->farba == "Medená blond"){
			$haveColor = true;
			$colorId = 56;
		}elseif($row->farba == "Gaštanová"){
			$haveColor = true;
			$colorId = 57;
		}elseif($row->farba == "Snehový blond"){
			$haveColor = true;
			$colorId = 58;
		}
		if($haveColor){
			$deleteImageQuery = 'DELETE from ps_product_attribute_image where id_product_attribute = ?';
			$deleteImage = $db->prepare($deleteImageQuery);
			$deleteImage->execute(array($row->id_product_attribute));
			$insertImageQuery = "INSERT INTO ps_product_attribute_image (id_product_attribute, id_image) VALUES (?,?)";
			$insertImage = $db->prepare($insertImageQuery);
			$insertImage->execute(array($row->id_product_attribute, $colorId));
		}
	}
?>
