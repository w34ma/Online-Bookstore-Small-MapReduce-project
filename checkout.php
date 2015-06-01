<?session_start();?>

<!DOCTYPE html>
<html>

<head>
	<title>Checkout</title>
	<link rel = "stylesheet" type = "text/css" href="style.css">
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
</head>

<body>
	<?php
		print "<h1 id = \"checkouttitle\">Shopping Cart Summary</h1><br/>";	
		print "<div><ul id = \"summary\">";
				$conn=mysqli_connect('sophia.cs.hku.hk','wcma','CA5&qiao') or die ('Failed to Connect '.mysqli_error($conn));
				mysqli_select_db($conn, 'wcma') or die ('Failed to AccessDB'.mysqli_error());
				$ids = implode(",",$_SESSION['shoppingCart']); 
		if(count($_SESSION['shoppingCart']) == 0){
			print "<h2 id = \"totalprice\">Your Shopping Cart is empty.</h2>";	
			print "<br/><a id = 'back' href = 'index.html'>Back to Online Bookstore</a>";
		}else{
				$query1 = 'select * from catalog where itemID in ('.$ids.')';
				$result1 = mysqli_query($conn, $query1) or die ('Failed to query1 '.mysqli_error($conn));
				$json = array();
				$total = 0;
				while($row=mysqli_fetch_array($result1)) {
					print "<div class=\"col-md-12\">";
					print "<li><div class=\"checkoutcat\" id=item".$row['itemID'].">";
					print "<div class=\"row\"><div class=\"col-md-3\" >";
					print "<img id= \"checkout_imagepart\" src=".$row['itemImage']."><br/>"; 
					print "</div><div class=\"col-md-6\" class = \"checkout_textpart\" id=".$row['itemID'].">";
					print "<h2 id = \"checkout_bookname\">".$row['itemName']."</h2>";
					print "<p id=\"checkout_destitle\">Description:</p>";
					print "<p id=\"checkout_des\">".$row['itemDescription']."</p>";
					print "<p id = \"checkout_price\">"."Price:  HK$ ".$row['itemPrice']."</p>";
					$total = $total+$row['itemPrice'] * $_SESSION['item'.$row['itemID']];
					$subtotal =$row['itemPrice'] * $_SESSION['item'.$row['itemID']];
					print "<p id = \"checkout_price\">"."Subtotal: ".$subtotal."</p>";
					print "<p id = \"checkout_price\">"."Quantity: ".$_SESSION['item'.$row['itemID']]."</p>";
					print "</div></div></div>"."</li><br/><br/></div>";
				}
				
	print "</ul></div><br/>"; 
	print "<h2 id = \"totalprice\">Total: HK$ ".$total."<br/><br/>";
	print "<form name = \"checkout\" action=\"confirmation.php\">";
	print "Name: "."<input type = \"text\" name = \"username\">"."<br/><br/>";
	print "Email : "."<input type = \"text\" name = \"email\">"."<br/><br/>";
	print "Billing Information: "."<input type = \"text\" name = \"billing\">"."<br/><br/>";
	print "<input type=\"submit\" value=\"Comfirm\">"."<br/>";}
	?>
</body>

</html>