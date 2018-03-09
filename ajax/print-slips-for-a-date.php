<?php 

/*******
	container logic:
  


***/
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "fpdf17/fpdf.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/order-items.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/allergies-master.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/containers.php");
	require_once($g_docRoot . "classes/container-items.php");
	require_once($g_docRoot . "classes/subscriptions.php");
	require_once($g_docRoot . "classes/subscription-items.php");

	define(MAX_ITEM_CHARS, 85);		// max chars per item row
class PDF extends FPDF
{

	function Header()
	{
		global $g_docRoot, $logofile, $id, $previewMode, $previewBackground, $previewAlign, $background, $align,  $useCustom, $customBackground, $previewCustomBackground, $previewUseCustom, $headerDone,
			$fontName, $fontBoldName;


					$this->SetFont($fontName,'B',12);
		$this->Ln(1);
		$headerDone = true;


	}

	function SetPage($num) {
   		 $this->page = $num;
	}


	function AcceptPageBreak()
	{
		global $currY, $pages;

		$currY = 5;
		$pages ++;
		
		return true;
		
	}	



}


$date = $_GET["date"];
$orderId = $_GET["order_id"];	// if orderid is specified then date is ignored
							// if subs is passed then process subscriptions intead of orders
$subsFlag = $_GET["subs"];

$reportDate = $date;

if ($subsFlag == 1)
  $subsWeekDay  = date("w", strtotime($reportDate));



if (!$date && !$orderId && !$subsWeekDay)
	exit("");

	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orderItems = new OrderItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$containers = new Containers($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$containerItems = new ContainerItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptions = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptionItems = new SubscriptionItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);




	$business_name = "JACK & JILL CATERING";

	// pdf params
	$background = "";
	$textColor = "#000000";
	$lineColor = "#d4d4d4";
	$align = "L";
	$useCustom = "0";
	$customBackground = "";
	$hideBusinessName = "0";

	$pages = 1;
	$cutoffMargin = 10;
	$pdf = new PDF();
	$pdf->SetTextColor(100,45,50);

	// font 
	$fontName = "Roboto";
	$fontFile = "Roboto-Regular.php";
	$fontBoldName = "Roboto";
	$fontBoldFile = "Roboto-Bold.php";

	$fontName = "YanoneKaffeesatz";
	$fontFile = "YanoneKaffeesatz-Regular.php";
	$fontBoldName = "YanoneKaffeesatz";
	$fontBoldFile = "YanoneKaffeesatz-Bold.php";

	$fontName = "NewsCycle";
	$fontFile = "NewsCycle-Regular.php";
	$fontBoldName = "NewsCycle";
	$fontBoldFile = "NewsCycle-Bold.php";

	$pdf->AddFont($fontName,'',$fontFile);
	$pdf->AddFont($fontBoldName,'B',$fontBoldFile);



	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(true, $cutoffMargin);

	if ($textColor != null && $textColor != "") {
		$arr = explode(",", $textColor);
		$red = $arr[0];
		$green = $arr[1];
		$blue = $arr[2];
		$pdf->SetTextColor($red, $green, $blue);

	}

	if ($lineColor != null && $lineColor != "") {
		$arr = explode(",", $lineColor);
		$red = $arr[0];
		$green = $arr[1];
		$blue = $arr[2];
		$pdf->SetDrawColor($red, $green, $blue);

	}

	$pdf->SetFont($fontName,'',20);
	$pdf->SetTextColor(32);
	if ($textColor != null && $textColor != "") {
		$arr = explode(",", $textColor);
		$red = $arr[0];
		$green = $arr[1];
		$blue = $arr[2];
		$pdf->SetTextColor($red, $green, $blue);

	}

	$pdf->SetFont($fontName,'',16);

	$currY = 5;
	$labelStartY = $currY;
	$currX = 10;
    $pdf->SetY($currY);


	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");

	if (!$orderId) {
		if (!$subsWeekDay) {
			$count = $orders->getCountForDeliveryDate($date);
			$rows = $orders->getRowsForDeliveryDate($date, 0, $count);
		} else {
			$count = $subscriptions->getActiveRowCountForWeekday($subsWeekDay, $reportDate);
			$rows = $subscriptions->getActiveRowsForWeekday($subsWeekDay, $reportDate, 0, $count);

		}
	} else {

		$count = 1;
		$row = $orders->getRowById("ID", $orderId);
		$rows = array();
		$rows[] = $row;
	}
	
	$counter = 0;

	// get school id for each row
	for($i = 0; $i < count($rows); $i++) {
		$row = $rows[$i];
		$studentRow = $students->getRowById("ID", $row["student_id"]);
		$row["school_id"] = $studentRow["school_id"];

		$rows[$i] = $row;
	}

	if (count($rows) >= 2) {
		// sort as per school id
		$sorted = false;
		while (!$sorted) {
			$sorted = true;
			for($i = 0; $i < count($rows)-1; $i++) {
				$row1 = $rows[$i];
				$row2 = $rows[$i+1];

				if ($row2["school_id"] < $row1["school_id"]) {
					$rows[$i] = $row2;
					$rows[$i+1] = $row1;
					$sorted = false;
				}
			}
		} // 	while (!$sorted) {

	} // 	if (count($rows) >= 2) {

	// extract unique school ids
	$schoolIds = array();
	foreach($rows as $row) {
		if (!in_array($row["school_id"], $schoolIds))
		  $schoolIds[] = $row["school_id"];
	}


	// print all slips for a school at a time
	foreach($schoolIds as $schoolId) {

	// o - print recess slips, 1 - print lunch slips, 2- lunch + recess slips
	for($loop=0; $loop < 3; $loop++) {
	
	foreach($rows as $row) {
		if ($row["school_id"] != $schoolId)
			continue;
		$memberRow = $members->getRowbyId("ID", $row["member_id"]);
		$studentRow = $students->getRowById("ID", $row["student_id"]);
		$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
		$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);

		if (!$subsWeekDay) {
			$itemCount = $orderItems->getCountForOrder($row["ID"]);
			$items = $orderItems->getGroupedRowsForOrder($row["ID"], 0, $itemCount);
		}
		else {
			$itemCount = $subscriptionItems->getCountForSubscription($row["ID"]);
			$items = $subscriptionItems->getGroupedRowsForSubscription($row["ID"], 0, $itemCount);

		}


		if (false) { /****TO DO ****/
		// check for meal deal and put items from there into
		foreach($items as $item) {
			if ($item["product_id"] ==  MEAL_DEAL_ITEM_DISPLAY_ID) {
				$mdItems = unserialize($mealDealRow["items"]);
				var_dump($mdItems);
				exit;	
			}
		}
		}
					
		// pur delivery date in subscription rows
		if ($subsWeekDay) {
			$row["delivery_date"] = $reportDate;
		}
					
				// fetch details of order
		$date = $row["date"];
		$delivery_date = $row["delivery_date"];
		$cname = $memberRow["name"];
		$email = $memberRow["email"];
		$phone = $memberRow["phone"];
		$notes = $row["notes"];
		
		$studentName = $studentRow["name"];
		$className = $classRow["name"];
		$schoolName = $schoolRow["name"];

		

			
		$total =  $row["net_total"];
		if ($total == '')
			$total = 0.00;

		$gtotal = $row["gross_total"];;
		if ($gtotal == '')
			$gtotal = 0;
		$tax_amt = $gtotal/11;

		
		$allergiesList = $allergies->getEnabledList(0, 100);
		
		$allergiesString = $studentRow["allergies"];
	    $allergyIds = implode("," , $allergiesString);
		$otherAllergies = $studentRow["other_allergies"];
		
		$allergyArr = explode(",", $studentRow["allergies"]);


		// filter items as per loop type
		$items2 = array();
		for($x =0; $x < count($items); $x++) {
			$item = $items[$x];
			if (
			    ($loop == 0 && $item["meal_type"] == "R") ||
				($loop == 1 && $item["meal_type"] == "L") ||
				($loop == 2 && $item["meal_type"] == "RL")
				)
			  $items2 [] = $item;

		}
		$items = $items2;
		if (count($items) == 0)
			continue;


		// divide items into hot and cold as per food type (0 - hot, 1 - cold
		for($itemType = 0; $itemType < 2; $itemType++) {
			if ($itemType == 0)
				$foodType = "HOT";
			else if ($itemType == 1)
				$foodType = "COLD";

		  	$items2 = array();
			for ($y=0; $y < count($items); $y++) {
				$item = $items[$y];
				$checkRow = $products->getRowById("ID", $item["product_id"]);
				if (
				    ($itemType == 0 && $checkRow["food_type"] == "HOT") ||
					($itemType == 1 && $checkRow["food_type"] == "COLD")
					)
					$items2 [] = $item;
			}

		  if (count($items2) < 1)
		  	continue;

		  $containersDone = false;
		  
		  // form array of item qtys
		  $arrQty = array();
		  foreach($items2 as $i) {
		  	if (array_key_exists($i["product_id"], $arrQty)) {
			  $qty = $arrQty[$i["product_id"]] ;
			  $arrQty[$i]["product_id"] = $qty + $i["qty"];
		    } else {
			   $arrQty +=  array($i["product_id"] => intval($i["item_qty"]));	
			}
		  }
		
		  // container logic goes here
		  while (!$containersDone) {

			  $printItems = array();
			  // find container with this combination
			  $arrPids = array();
			  foreach($arrQty as $key=>$value) {
			  	$arrPids[] = $key;
			  }
			  $containerItemRow = $containerItems->getContainerForACombination($arrPids);
			  // if no container found
			  if (!$containerItemRow || count($containerItemRow) == 0) {
			  	
				// take first item in list and print it with qty=1. decrement qty from arrQty
				reset($arrQty);
				$firstItem = key($arrQty);
	
				for($i = 0; $i < count($items2); $i++) {
					$item2 = $items2[$i];
					if ($item2["product_id"] == $firstItem) {
						$printItem = $item2;
						$currQty = $arrQty[$firstItem];
						$newQty = $currQty - 1;
						$printItem["item_qty"] = 1;
						$printItems[] = $printItem;
						
						$item2["item_qty"] -= 1;
						$items2[$i] = $item2;

						if ($newQty < 1)
							unset($arrQty[$firstItem]);
						else
							 $arrQty[$firstItem] = $newQty;
						break;
					}
				}
			  } //  if (!$containerItemRow || count($containerItemRow) == 0) {
			  else {
			 
			 	$containerId = $containerItemRow["container_id"];
				$cItemRows = $containerItems->getListForAContainer($containerId, 0, 100);

				// adjust qtys in arrQty and also in printing items
				foreach($cItemRows as $cirow) {
					$cqty = $cirow["qty"];
					$cpid = $cirow["product_id"];
					$currQty = $arrQty[$cpid];
					if ($currQty >= $cqty)
						$newQty = $currQty - $cqty;
					else {
						$newQty = $currQty-1;
						$cqty = 1;

					}
			    	if ($arrQty[$cpid] == null) {
						continue;
					}
					if ($newQty >0) {
						$arrQty[$cpid] = $newQty;

					} else {
						$arrQty[$cpid] = 0;
					}

					for($i = 0; $i < count($items2); $i++) {
						$item2 = $items2[$i];	
						if ($item2["product_id"] == $cpid) {
								$printItem = $item2;
								$printItem["item_qty"] = $cqty;
								$printItems[] = $printItem;
								$item2["item_qty"] -= $cqty;

								if ($item2["item_qty"] < 0)
									$item2["item_qty"] = 0;
								$items2[$i] = $item2;


								// check if this item has to be removed from arrQty
								if ($arrQty[$cpid] == 0) {
									unset($arrQty[$cpid]);
								}
									
								break;

						}
					}

						


				}

				
			  } //  if (!$containerItemRow || count($containerItemRow) == 0) else

			  
			 
			  // check if container logic is done
			  if ($arrQty == null || count($arrQty) == 0)
			  	$containersDone = true;
			  
			  
			  $pdf->SetFont($fontName,'',8);
			  $pdf->SetFillColor(240);
			  $pdf->Cell(8, 10, "School:", 0, 0, 'L', false);
			  $currX = $pdf->GetX() + 15;
			  $pdf->SetFont($fontName,'',12);
			  $pdf->Cell(50, 10, $schoolRow["name"], 0, 0, 'L', false);
			  $currY = $pdf->GetY();
			  $pdf->SetY($currY + 5);

			  if (strlen($classRow["name"]) >3) {
				  $pdf->SetFont($fontName,'',8);
				  $pdf->SetFillColor(240);
				  $pdf->Cell(8, 10, "Class:", 0, 0, 'L', false);
				  $currX = $pdf->GetX() + 15;
				  $pdf->SetFont($fontName,'',12);
				  $pdf->Cell(50, 10, $classRow["name"], 0, 0, 'L', false);
				  $currY = $pdf->GetY();
				  $pdf->SetY($currY + 5);

			  }
			  
			  $pdf->SetFont($fontName,'',8);
			  $pdf->SetFillColor(240);
			  $pdf->Cell(8, 10, "Name:", 0, 0, 'L', false);
			  $currX = $pdf->GetX() + 15;
			  $pdf->SetFont($fontName,'',12);
			  $pdf->Cell(50, 10, $studentRow["name"], 0, 0, 'L', false);
			  $currY = $pdf->GetY();
			  $pdf->SetY($currY + 5);

			  $pdf->SetFont($fontName,'',8);
			  $pdf->Cell(8, 10, "Order#:", 0, 0, 'L', false);
			  $currX = $pdf->GetX() + 10;
			  $pdf->SetX($currX);
			  $pdf->SetFont($fontName,'',10);
			  $pdf->Cell(50, 10, $row["ID"], 0, 0, 'L', false);
			  $currY = $pdf->GetY();
			  $pdf->SetY($currY + 5);

			  $pdf->SetFont($fontName,'',8);
			  $pdf->Cell(8, 10, "Delivery Date:", 0, 0, 'L', false);
			  $currX = $pdf->GetX() + 10;
			  $pdf->SetX($currX);
			  $pdf->SetFont($fontName,'',10);
			  $pdf->Cell(50, 10, date("Y-M-d", strtotime($delivery_date)), 0, 0, 'L', false);
			  $currY = $pdf->GetY();
			  $pdf->SetY($currY + 5);

			  $pdf->SetFont($fontName,'',8);
			  $pdf->Cell(8, 10, "Food Type:", 0, 0, 'L', false);
			  $currX = $pdf->GetX() + 10;
			  $pdf->SetX($currX);
			  $pdf->SetFont($fontName,'',10);
			  $pdf->Cell(50, 10, $foodType, 0, 0, 'L', false);
			  $currY = $pdf->GetY();
			  $pdf->SetY($currY + 8);

			  foreach($allergiesList as $allergy) {
				  if (in_array($allergy["ID"], $allergyArr)) {
					  $pdf->SetFont($fontName,'',8);
					  $pdf->SetFillColor(245,17,59);
					  $pdf->Cell(50, 10, $allergy["name"], 0, 0, 'L', true);
					  $currY = $pdf->GetY();
					  $pdf->SetY($currY + 9);

				  }
			  } 
			  if ($studentRow["other_allergies"] != null && $studentRow["other_allergies"] != "") {
				  $pdf->SetFont($fontName,'',8);
				  $pdf->SetFillColor(245,17,59);
				  $pdf->Cell(50, 10, $studentRow["other_allergies"], 0, 0, 'L', true);
			  }
			  $currY = $pdf->GetY()+10; 		

			  $mt = "";


			  
			  foreach($printItems as $item) {
				  if ($mt != $item["meal_type"]) {
					  $mt = $item["meal_type"];
					  if ($item["meal_type"] == "L")
						  $mealType = "LUNCH";
					  else if($item["meal_type"] == "R")
						  $mealType = "RECESS";
					  else if ($item["meal_type"] == "RL")
						  $mealType = "RECESS+LUNCH";

					  $pdf->SetY($currY + 12);
					  $pdf->SetFont($fontName,'',16);
					  $pdf->SetX(10);
					  $pdf->Cell(40, 10, $mealType . ":", 1, 0, 'L', false);

					  $currY = $pdf->GetY()+15;

				  }
				  else
					  $pdf->SetY($currY + 12);

				  if ($item["product_id"] ==  MEAL_DEAL_ITEM_DISPLAY_ID) {
					  $item["productname"] = $mealDealRow["name"];
					  $item["image"] = $mealDealRow["image"];

				  }

				  $pdf->SetY($currY);
				  $pdf->SetFont($fontName,'',12);
				  $pdf->SetX(10);
				  if ($item["item_qty"] > 1) {
					  $pdf->SetFillColor(0,141,235);
					  $pdf->Cell(50, 10, $item["item_qty"] . " x " . $item["productname"], 0, 0, 'L', true);

				  }
				  else 
					  $pdf->Cell(30, 10, $item["item_qty"] . " x " . $item["productname"], 0, 0, 'L', false);
				  $currY += 8;	
				  $pdf->SetY($currY);
			  }

			  $maxY = $pdf->GetY();

			  // only print class here if its less than 3 chars
			  if (strlen($classRow["name"]) < 3) {
				  $pdf->SetFont($fontName,'B',38);
				  $pdf->SetY($labelStartY);
				  $pdf->SetX(70);
				  $pdf->Cell(20, 40, $classRow["name"], 1, 0, 'C', false);
			  } 

			  if ($schoolRow["image"] == null || $schoolRow["image"] == "")
				  $imageFile = $g_docRoot . "images/school.jpg";
			  else
				  $imageFile = $g_docRoot . "schools/files/" . $schoolRow["image"];

			  $f = fopen($imageFile, "r");
			  if ($f) {
				  fclose($f);
				  $extension = strtolower(substr($logofile, strlen($logofile)-3, 3));
				  // create background image file
				  switch ($extension) {
					  case 'jpg':
						  $img = imagecreatefromjpeg($imageFile);
						  break;
					  case 'jpe':
						  $img = imagecreatefromjpeg($imageFile);
						  break;
					  case 'png':
						  $img = imagecreatefrompng($imageFile);
						  break;
					  case 'gif':
						  $img = imagecreatefromgif($imageFile);
						  break;
					  default:
						  $img = imagecreatefromjpeg($imageFile);
				  }	

				  if (!$img) {
					  //exit("Error creating background image");
				  }
				  if (!imagefilter($img, IMG_FILTER_GRAYSCALE)) {
					  //exit("Could not apply image filter");
				  }
				  if (!imagefilter($img, IMG_FILTER_BRIGHTNESS, 195)) {
					  //exit("Could not apply image filter");
				  }

				  imagedestroy($img);
				  $pdf->Image($imageFile,90, $labelStartY, 50, 40);
				  $pdf->Rect(90,$labelStartY,50,40);
			  } // 	if ($f) {

			  if ($row["notes"] != null && $row["notes"] != "") {
				  $pdf->SetFont($fontName,'',10);
				  $pdf->SetY(49);
				  $pdf->SetX(70);
				  $pdf->SetFillColor(235, 235, 0);
				  $pdf->MultiCell(70, 10, $row["notes"], 0, 0, 'L', true);

			  }

			  $pdf->Rect(3,$labelStartY,137,($maxY - $labelStartY+5));
			  $labelStartY = $maxY + 10;
			  $pdf->SetY($labelStartY);

			  $counter++;

			// print only two slips per page
			if ($counter == 2) {
				$counter = 0;
				$pdf->AddPage();
				$currY = 5;
				$labelStartY = $currY;
				$currX = 10;
				$pdf->SetY($currY);

			}
		
		} //  while (!$containersDone) {

	  } // 	for($itemType = 0; $itemType < 2; $itemType++) {


	} // 	foreach($rows as $row) {

	} // 	for($loop=0; $loop < 2; $loop++) {

	} // foreach($schoolIds as $schoolId)

		if (!$orderId) {
			if (!$subsWeekDay)
				$filename = $g_docRoot . "output/" . date("Y-m-d", strtotime($reportDate)) . "-slips.pdf";
			else
				$filename = $g_docRoot . "output/" . date("Y-m-d", strtotime($reportDate)) . "-subs-slips.pdf";
			
		} else
			$filename = $g_docRoot . "output/" . $orderId . "-slips.pdf";
		
		$pdf->Output($filename,'F');
	
		if (!$orderId) {
			if (!$subsWeekDay)
				exit($g_webRoot . "output/" . date("Y-m-d", strtotime($reportDate)) . "-slips.pdf");	
			else
				exit($g_webRoot . "output/" . date("Y-m-d", strtotime($reportDate)) . "-subs-slips.pdf");	
		} else
		    exit($g_webRoot . "output/" . $orderId . "-slips.pdf");


?>
