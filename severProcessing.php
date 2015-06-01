<?session_start();?>

<?php
	if($_GET['addflag'] == 1){
		if(!isset($_SESSION['shoppingCart'])){
			$_SESSION['shoppingCart'] = array();
		}
		if(!in_array($_GET['catid'],$_SESSION['shoppingCart'])){
			array_push($_SESSION['shoppingCart'],$_GET['catid']);
			$keyname1 = 'item'.$_GET['catid'];
			$_SESSION[$keyname1] = 1;
		}
	}else{
		if($_GET[displayflag] == 0){
			$conn=mysqli_connect('sophia.cs.hku.hk','wcma','CA5&qiao') or die ('Failed to Connect '.mysqli_error($conn));
			mysqli_select_db($conn, 'wcma') or die ('Failed to AccessDB'.mysqli_error());
			$query = 'select * from catalog limit '.$_GET['lastRecord'].', 2';

			$result = mysqli_query($conn, $query) or die ('Failed to query '.mysqli_error($conn));
			$json = array();
   
			while($row=mysqli_fetch_array($result)) {
				$json[]=array('itemID'=>$row['itemID'], 'itemName'=>$row['itemName'], 'itemDescription'=>$row['itemDescription'],'itemImage'=>$row['itemImage'],'itemPrice'=>$row['itemPrice']);
			}
		}
		if($_GET[displayflag] == 1 && count($_SESSION['shoppingCart']) != 0){
				$conn=mysqli_connect('sophia.cs.hku.hk','wcma','CA5&qiao') or die ('Failed to Connect '.mysqli_error($conn));
				mysqli_select_db($conn, 'wcma') or die ('Failed to AccessDB'.mysqli_error());
				$ids = implode(",",$_SESSION['shoppingCart']); 
				$query1 = 'select * from catalog where itemID in ('.$ids.')';
				$result1 = mysqli_query($conn, $query1) or die ('Failed to query1 '.mysqli_error($conn));
				$json = array();
				while($row=mysqli_fetch_array($result1)) {
						$json[]=array('itemID'=>$row['itemID'], 'itemName'=>$row['itemName'], 'itemDescription'=>$row['itemDescription'],'itemImage'=>$row['itemImage'],'itemPrice'=>$row['itemPrice']);
				}

			if($_GET[updateflag] == 1){
				$keyname = 'item'.$_GET['cartcatid'];
				$_SESSION[$keyname] = $_GET['num'];
				if($_SESSION[$keyname] == 0){
					for($i = 0; $i < count($json);$i++){
						if($json[$i][itemID] == $_GET['cartcatid']) {
							unset($json[$i]);
							$json=array_values($json);
							break;
						}
					}	
					//if(($key = array_search($_GET['cartcatid'],$_SESSION['shoppingCart'])) != false){
					$key = array_search($_GET['cartcatid'],$_SESSION['shoppingCart']);
					unset($_SESSION['shoppingCart'][$key]);
						//$_SESSION['shoppingCart']=array_values($_SESSION['shoppingCart']);
					//}
				}
			}
		}
		print json_encode(array('cat'=>$json));
	}
?>