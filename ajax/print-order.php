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

	define(MAX_ITEM_CHARS, 85);		// max chars per item row
class PDF extends FPDF
{

	function Header()
	{
		global $g_docRoot, $logofile, $id, $previewMode, $previewBackground, $previewAlign, $background, $align,  $useCustom, $customBackground, $previewCustomBackground, $previewUseCustom, $headerDone,
			$fontName, $fontBoldName;


				// add logo only if image is present
			
		$imageFile = $g_docRoot . "images/logo.jpg";
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
	
			// only print header image in first page , not on successive pages
				if (!$headerDone) {
					if ($background != null && $background != "") 
						$this->Image($g_docRoot . "templates/backgrounds/" . $background,0, 
								2, $this->w, 40);
				}
				
			$leftCol = 10;
			if ($align == "L")
				$leftCol = 12;
			if ($previewAlign == 'L')
				$leftCol = 170;

			if ($logofile && !$headerDone) {
				$this->Image($g_docRoot . "images/logo.jpg",$leftCol,10,60);
			}
		}	
		$this->SetFont($fontName,'B',12);
		$this->Ln(1);
		$headerDone = true;


	}

	function Signature() {
		$this->SetY(220);
		$this->Ln();
		$this->Cell(60,6,"Contractor Signature",0,0,'L',false);

		$this->Cell(140, 5, "Client Signature", 0, 1, 'R', false);
	
	}
	
	function Footer()
	{
		global $order_notes, $fontName, $fontBoldName;

		$this->SetY(-20);
		$this->SetX(10);
    	$this->SetFont($fontName,'',10);
		$this->MultiCell(0,5, "Special Instructions:\r\n" . $order_notes, 0,'L');

	}

	function ChapterTitle($num, $label, $ypos=50)
	{
		global $fontName, $fontBoldName;

		$this->SetY($ypos);
		$this->SetFont($fontName,'B',12);
		$this->Cell(20,6,$num,0,0,'L',0);
		$this->SetFont($fontName,'',12);
		$this->Cell(110,6,$label,0,1,'L',0);
		$this->Ln(0);
    }

	function ChapterTitle2($num, $label)
	{
		global $fontName, $fontBoldName;
	
		$this->SetY(20);
		$this->SetFont($fontName,'',12);
		$this->Cell(110,6,"$num $label",0,1,'L',0);
	}

	function BillTo($studentName, $class, $school) {
		global $fontName, $fontBoldName;

		$this->SetY(72);
		$this->SetFont($fontName,'B',12);
		$this->SetFillColor(240);
		$this->Cell(20, 5, "For:", 0, 0, 'L', false);
		
		$this->SetFont($fontName,'',12, true);
		$this->SetFillColor(240);

		$this->SetFont($fontName,'',12, true);
		$this->Cell(110,6,$studentName,0,1,'L', 0);
		$this->SetX(30);
		$this->MultiCell(110,6,"Class " . $class . ", " . $school,0,1,0);
	}

	function OpenTag($tag,$attr)
{
    //Opening tag
    if($tag=='B' or $tag=='I' or $tag=='U')
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF=$attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
}

function CloseTag($tag)
{
    //Closing tag
    if($tag=='B' or $tag=='I' or $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF='';
}

function SetStyle($tag,$enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
        if($this->$s>0)
            $style.=$s;
    $this->SetFont('',$style);
}

function PutLink($URL,$txt)
{
    //Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}
	function WriteHTML($html)
	{
    //HTML parser
    $html=str_replace("\n",' ',$html);
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            //Tag
            if($e{0}=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                    if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                $this->OpenTag($tag,$attr);
            }
        }
    }
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

	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");


	$row = $orders->getRowbyId("ID", $id);
	$memberRow = $members->getRowbyId("ID", $row["member_id"]);
	$studentRow = $students->getRowById("ID", $row["student_id"]);
	$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
	$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);

	$itemCount = $orderItems->getCountForOrder($row["ID"]);
	$items = $orderItems->getRowsForOrder($row["ID"], 0, $itemCount);

	
	$business_name = "JACK & JILL CATERING";
	$logofile =  $g_docRoot . "images/logo.jpg";
	$address= "12 Christchurch Street";;
	$area= "Willingdon Sydney";
	$postcode= "1711";;
	$abn= "1818181";
	$acn= "81181";
	$website = "http://www.jackandjill.com.au";
	$phones = "18180-190";
		
	$bank_details = "BSB: 18181 A/c No: 19181-10 A/c Name: Jack&Jill LLC";

	$order_notes= $row["notes"];
	$from_emailid = "support@jackandjill.com.au"; 
		
	$order_mail = nl2br("quot mail");
	$order_terms = "Terms and Conditions come here";
		
	$show_three_column = "1";				

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
		
			// check alignment coords
		$alignType = "R";
		if ($align != null && $align != '') {
			$alignType = $align;
		}
		

		if (!$hideBusinessName == 1)
			$pdf->Cell(0,45,$business_name,0,1,$alignType);
		$pdf->Line(10, 47, $pdf->w-5, 47);
		

		
		$pdf->SetFont($fontName,'',16);
		$pdf->Cell(0,30,'',0,1,$alignType);
		$pdf->BillTo($studentRow["name"], $classRow["name"], $schoolRow["name"]);
		$headerEndRow = $pdf->GetY();

		$pdf->ChapterTitle('Order #: ',$row["ID"]);
		$pdf->ChapterTitle('Invoice#: ',$row["invoice"], 55);

		$pdf->ChapterTitle('Date: ',date('d-M-Y', strtotime($row["date"])), 60);
		$pdf->ChapterTitle('Delivery: ',date('d-M-Y', strtotime($row["delivery_date"])), 65);
		
		if ($pdf->GetY() > $headerEndRow)
			$headerEndRow = $pdf->GetY();
	
		$pdf->SetFont($fontName,'B',14);
		$pdf->SetY(50);
		$pdf->Cell(0,6,"Order Invoice",0,1,'R');
		$pdf->SetFont($fontName,'',12);
		$pdf->Cell(0,6,$address . " " . $area . " " . $postcode,0,1,'R');
		$pdf->SetFont($fontName,'',12);
		if ($phones != null && trim($phones) != "") 
			$pdf->Cell(0,6, "Ph.:" . $phones, 0,1, 'R');
		if ($website != null && trim($website) != "") {
			$websiteText = str_replace("http://", "", $website);
			$siteLink = "<a href='" . $website . "'>" .  $websiteText . "</a>";
			$saveY = $pdf->GetY();
			$pdf->SetY($saveY);
			$pdf->SetX($pdf->w- strlen($siteLink)+16.5);
			$pdf->WriteHTML($siteLink);
			$pdf->SetY($saveY);
			$pdf->Cell(0,6, "", 0,1,'R');


		}
		$pdf->Cell(0,6, "ABN: " . $abn. " ACN: " . $acn,0,1,'R');


		$pdf->SetFont($fontName,'B',12);
		$pdf->Cell(0,20,'',0,1,'R');
		
		$pdf->Cell($itemWidth ,7,'Item',1,0,'L');
		if ($show_three_column == "1") {
			$pdf->Cell($qtyXPos,7,'Qty',1,0,'R');	
			$pdf->Cell($rateXPos,7,'Rate',1,0,'R');	
		}		
		$pdf->Cell($priceXPos,7,'Price',1,1,'R');

		$pdf->SetFont($fontName,'',12);
		$pdf->Ln(2);

		foreach($items as $item) {
 		 if ($item["product_id"] ==  MEAL_DEAL_ITEM_DISPLAY_ID) {
			  $item["productname"] = $mealDealRow["name"];
			  $item["image"] = $mealDealRow["image"];

	     }
		
		  $item1 = $item["productname"];
		  $rate1 = number_format($item["item_price"],2);
		  $qty1 = $item["item_qty"];
		  $price1 = $qty1 * $rate1;
		  
		  if (strlen($item1) > MAX_ITEM_CHARS || strpos($item1, "\n") > -1) {
			if (substr($item1, strlen($item1)-1,1) != "\n")
					$item1 .= "\n";
		
			$currY = $pdf->GetY();
			$currX = $pdf->GetX();
			$pdf->MultiCell($itemWidth ,5,$item1,0,'L',0);


			$nextY = $pdf->GetY(); // save curr.y position so that next item starts from here

			$pdf->SetXY(180, $currY); // place cursor on same y as top item of item

			$countLines= substr_count($item1, "\n");
			$blankRows = "";
			for($x = 0; $x < $countLines; $x++) {
				$blankRows .= "\n";
			}
			$pdf->MultiCell($priceXPos,5, "$" . number_format($price1,2) . $blankRows,0,'R');

			$pdf->SetXY(0, $nextY);

		} else {
			$pdf->Cell($itemWidth ,7,$item1,0,0,'L',0);
			if ($show_three_column == "1") {
				$pdf->Cell($qtyXPos,7, $qty1,0,0,'R');	
				$pdf->Cell($rateXPos,7, "$" . number_format($rate1,2),0,0,'R');	
			}		
			
			$pdf->Cell($priceXPos,7, "$" . number_format($price1,2),0,1,'R',0);
		}
		$pdf->Cell(0,0,'',0,1,'R');
		$pdf->Ln(2);

	} // foreach($items as $item

		
		$pdf->Cell(0,0,'',0,1,'R');
		$pdf->Line(10, $pdf->GetY(), $pdf->w-5, $pdf->GetY());

		if ($show_three_column == "1") {
			$pdf->Cell($itemWidth + $rateXPos + $qtyXPos,7,'Gross Total',0,0,'R',0);
			$pdf->Cell($priceXPos,7, "$" . number_format($gtotal,2),0,1,'R',0);
		} else {
			$pdf->Cell($itemWidth ,7,'Gross Total',0,0,'R',0);
			$pdf->Cell($priceXPos,7, "$" . number_format($gtotal,2),0,1,'R',0);
		}

		if ($tax_percent > 0) {
			//$pdf->Cell($itemWidth ,7,$tax_label . " @ " . number_format($tax_percent,2) . "%",1,0,'R',0);
			if ($show_three_column == "1") {
				$pdf->Cell($itemWidth + $rateXPos + $qtyXPos, 7,$tax_label ,0,0,'R',0);
				$pdf->Cell($priceXPos,7, "$" . number_format($tax_amt, 2),0,1,'R',0);

			} else {
				$pdf->Cell($itemWidth ,7,$tax_label ,0,0,'R',0);
				$pdf->Cell($priceXPos,7, "$" . number_format($tax_amt, 2),0,1,'R',0);
			}						
		

		}
		
		$pdf->SetFont($fontName,'B',12);
	

		if ($show_three_column == "1") {
			$pdf->Cell($itemWidth + $rateXPos + $qtyXPos,7,'Net Total',0,0,'R',0);
			$pdf->Cell($priceXPos,7, "$" . number_format($total,2),0,0,'R',0);

		} else {
			$pdf->Cell($itemWidth ,7,'Net Total',0,0,'R',0);
			$pdf->Cell($priceXPos,7, "$" . number_format($total,2),0,0,'R',0);
		}			
		$pdf->Cell(0,20,'',0,1,'R');

		$pdf->Cell(0,5,$pay,0,1,'L');
	
		/*$pdf->SetFont($fontName,'B',14);
		$pdf->Cell(0,6,"Bank Details",0,1,'L');
		$pdf->SetFont($fontName,'',12);

		$pdf->SetFillColor(240);
		$pdf->MultiCell(135,5,$bank_details,0,1);
		$pdf->Cell(0,20,'',0,1,'R');
		$pdf->Cell(190,40,$com,0,0,'C');*/

		//$pdf->Signature();


				// terms and conditions on next page
		/*if ($order_terms != null && trim($order_terms) != "") {	
			$pdf->AddPage();
			$pdf->SetFont('Times','B',20);
			$pdf->SetTextColor(32);
			$pdf->Cell(0,20,'Terms And Conditions',0,1,'C');
			$pdf->Ln();
			$pdf->SetFont('Times','',10);
			$pdf->MultiCell(0,4,$order_terms,0,'L',0);

		}	*/
		

		$filename = $g_docRoot . "output/" . $row["invoice"]. ".pdf";
		$pdf->Output($filename,'F');
			

?>
