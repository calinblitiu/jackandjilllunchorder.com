<?php 
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

}

$id =  $_POST['id'];

if ($id < 1)
	exit("");


	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orderItems = new OrderItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");


	$row = $orders->getRowbyId("ID", $id);
	$memberRow = $members->getRowbyId("ID", $row["member_id"]);
	$studentRow = $students->getRowById("ID", $row["student_id"]);
	$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
	$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
	$itemCount = $orderItems->getCountForOrder($row["ID"]);
	$items = $orderItems->getGroupedRowsForOrder($row["ID"], 0, $itemCount);

	
	$business_name = "JACK & JILL CATERING";

		$background = "";
		$textColor = "#000000";
		$lineColor = "#d4d4d4";
		$align = "L";
		$useCustom = "0";
		$customBackground = "";
		$hideBusinessName = "0";
					
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

		

		if ($show_three_column == "1") {
			$qtyXPos = 20;
			$rateXPos = 20;
			$priceXPos = 25;
			$itemWidth = 130;
			
		} // 	if ($show_three_column== "1") {
		else {
			$priceXPos = 25;
			$itemWidth = 170;

		}
			
		$total =  $row["net_total"];
		if ($total == '')
			$total = 0.00;

		$gtotal = $row["gross_total"];;
		if ($gtotal == '')
			$gtotal = 0;
		$tax_amt = $gtotal/11;

					// generate quotation 
		
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
		$currX = 10;
		
		$allergiesList = $allergies->getEnabledList(0, 100);
		
		$allergiesString = $studentRow["allergies"];
	    $allergyIds = implode("," , $allergiesString);
		$otherAllergies = $studentRow["other_allergies"];
		
		$allergyArr = explode(",", $studentRow["allergies"]);



		  $pdf->SetY($currY);
		  $pdf->SetFont($fontName,'',8);
		  $pdf->SetFillColor(240);
		  $pdf->Cell(8, 10, "Name:", 0, 0, 'L', false);
		  $currX = $pdf->GetX() + 15;
		  $pdf->SetFont($fontName,'',12);
		  $pdf->Cell(50, 10, $studentRow["name"], 0, 0, 'L', false);
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
			  $currY = $pdf->GetY();

		  }
 		  
		$mt = "";
		
		foreach($items as $item) {
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
 		 if ($item["product_id"] ==  MEAL_DEAL_ITEM_DISPLAY_ID) {
			  $item["productname"] = $mealDealRow["name"];
			  $item["image"] = $mealDealRow["image"];

	     }
		 
  		  $pdf->SetY($currY);
		  $pdf->SetFont($fontName,'',12);
		  $pdf->SetX(10);
		  if ($item["item_qty"] > 1) {
		  	$pdf->SetFillColor(0,141,235);
			$pdf->Cell(30, 10, $item["item_qty"] . " x " . $item["productname"], 0, 0, 'L', true);

		  }
		  else 
		    $pdf->Cell(30, 10, $item["item_qty"] . " x " . $item["productname"], 0, 0, 'L', false);
		  $currY += 8;	
		  $pdf->SetY($currY);
		}

		$maxY = $pdf->GetY();

  		$pdf->SetFont($fontName,'B',38);
		$pdf->SetY(5);
		$pdf->SetX(70);
		$pdf->Cell(20, 40, $classRow["name"], 1, 0, 'C', false);
		

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
			$pdf->Image($imageFile,90, 5, 50, 40);
			$pdf->Rect(90,5,50,40);
		} // 	if ($f) {

		if ($row["notes"] != null && $row["notes"] != "") {
			$pdf->SetFont($fontName,'',10);
			$pdf->SetY(49);
			$pdf->SetX(70);
			$pdf->SetFillColor(235, 235, 0);
			$pdf->MultiCell(70, 10, $row["notes"], 0, 0, 'L', true);
			
		}
		
		$pdf->Rect(3,5,137,$maxY);


		$filename = $g_docRoot . "output/" . $row["invoice"]. "-slips.pdf";
		$pdf->Output($filename,'F');

			

?>
