<?php 
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "fpdf17/fpdf.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/school-items.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/categories.php");

	define(MAX_ITEM_CHARS, 85);		// max chars per item row
class PDF extends FPDF
{

	function Header()
	{
		global $g_docRoot, $logofile, $id, $previewMode, $previewBackground, $previewAlign, $background, $align,  $useCustom, $customBackground, $previewCustomBackground, $previewUseCustom, $headerDone,
			$fontName, $fontBoldName;

	 if  ($headerDone)
	 	return;

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

	function SetPage($num) {
   		 $this->page = $num;
	}


function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
	global $currY, $pages, $printingLunch;

	$allowBreak = true;
	if ($printingLunch && $this->AutoPageBreak != true)
		$allowBreak = false;


		
	// Output a cell
	$k = $this->k;
	if($allowBreak && $this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
	{
		// Automatic page break
		$x = $this->x;
		$ws = $this->ws;
		if($ws>0)
		{
			$this->ws = 0;
			$this->_out('0 Tw');
		}
		$this->AddPage($this->CurOrientation,$this->CurPageSize);
		$this->x = $x;
		if($ws>0)
		{
			$this->ws = $ws;
			$this->_out(sprintf('%.3F Tw',$ws*$k));
		}
	}
	if($w==0)
		$w = $this->w-$this->rMargin-$this->x;
	$s = '';
	if($fill || $border==1)
	{
		if($fill)
			$op = ($border==1) ? 'B' : 'f';
		else
			$op = 'S';
		$s = sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
	}
	if(is_string($border))
	{
		$x = $this->x;
		$y = $this->y;
		if(strpos($border,'L')!==false)
			$s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'T')!==false)
			$s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
		if(strpos($border,'R')!==false)
			$s .= sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'B')!==false)
			$s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	}
	if($txt!=='')
	{
		if($align=='R')
			$dx = $w-$this->cMargin-$this->GetStringWidth($txt);
		elseif($align=='C')
			$dx = ($w-$this->GetStringWidth($txt))/2;
		else
			$dx = $this->cMargin;
		if($this->ColorFlag)
			$s .= 'q '.$this->TextColor.' ';
		$txt2 = str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
		$s .= sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt2);
		if($this->underline)
			$s .= ' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
		if($this->ColorFlag)
			$s .= ' Q';
		if($link)
			$this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
	}
	if($s)
		$this->_out($s);
	$this->lasth = $h;
	if($ln>0)
	{
		// Go to next line
		$this->y += $h;
		if($ln==1)
			$this->x = $this->lMargin;
	}
	else
		$this->x += $w;
}

	function AcceptPageBreak()
	{
		global $currY, $pages, $printingLunch;

		$currY = 10;

		//echo("new page at " . $pages . ", lunch flag=" . $printingLunch . "<br>");
		if ($printingLunch) {
		   
		   $this->SetPage(2);
		   return false;
		} else
			$pages ++;
		return true;
		
	}	

	function Signature() {
		$this->SetY(220);
		$this->Ln();
		$this->Cell(60,6,"Contractor Signature",0,0,'L',false);

		$this->Cell(140, 5, "Client Signature", 0, 1, 'R', false);
	
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


	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		$userId = 0;	
	}


	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schoolItems = new SchoolItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	// get meal deal items
	$mealDealRow = $mealdeal->getRowById("ID", "1");
	$mealDealItems = unserialize($mealDealRow["items"]);
	$mdItems = array();

	foreach($mealDealItems as $mdItem) {
			if ($mdItem["product_id"] == "0")
				continue;
			$pRow = $products->getRowById("ID", $mdItem["product_id"]);
			$mdItems[] = $pRow;
	}

	$studentId = $_GET["student"];
	$schoolId = $_POST["school"];
	if ($studentId) {
		$studentModePrint = true;
		$studentRow = $students->getRowById("ID", $studentId);
		$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
	}
	if ($schoolId > 0) {
		$schoolModePrint = true;
		$schoolRow = $schools->getRowById("ID", $schoolId);
	}
	$classRow = $classes->getRowById("ID", $studentRow["class_id"]);

	// get disabled items in school
	$dCount = $schoolItems->getCountForASchool($schoolRow["ID"]);
	$dRows = $schoolItems->getListForASchool($schoolRow["ID"], 0, $dCount);
	$disabledItems = array();
	foreach($dRows as $dRow) {
		$disabledItems [] = $dRow["product_id"];
	}


	$weekDays = "";
	if ($schoolRow["work_sun"] == 1)
		$weekDays .= "Sun ";
	if ($schoolRow["work_mon"] == 1)
		$weekDays .= "Mon ";
	if ($schoolRow["work_tue"] == 1)
		$weekDays .= "Tue ";
	if ($schoolRow["work_wed"] == 1)
		$weekDays .= "Wed ";
	if ($schoolRow["work_thu"] == 1)
		$weekDays .= "Thu ";
	if ($schoolRow["work_fri"] == 1)
		$weekDays .= "Fri ";
	if ($schoolRow["work_sat"] == 1)
		$weekDays .= "Sat ";
		

	//get categories
	$catCount = $cats->getCount();
	$catRows = $cats->getList( 0, $catCount, "name asc");
		
	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");

	// get menu items
	$rowCount = $products->getCount($name, null, null, null, null, null);
	$productRows = $products->getList($name, null, null, null, null, null, 0, $rowCount, $sort);

	// remove items which are in school  disabled list
	if ($disabledItems != null && $disabledItems != "") {

		$rows2 = array();
		foreach($productRows as $row) {
			if (!in_array($row["ID"], $disabledItems)) {
				$rows2 [] = $row;
			}
		}	
		$productRows = $rows2;
	}

	$mainProductRows = $productRows;
	

	$business_name = "JACK & JILL CATERING";
	$logofile =  $g_docRoot . "images/logo.jpg";
	$address= "12 Christchurch Street";;
	$area= "Willingdon Sydney";
	$postcode= "1711";;
	$abn= "1818181";
	$acn= "81181";
	$website = "http://www.jackandjill.com.au";
	$phones = "18180-190";
		
	$background = "";
	$textColor = "#000000";
	$lineColor = "#d4d4d4";
	$align = "L";
	$useCustom = "0";
	$customBackground = "";
	$hideBusinessName = "0";

					
	$headerDone = false;

	// generate menu 
		$pages = 1;
		$cutoffMargin = 20;
		$pdf = new PDF();
		$pdf->SetAutoPageBreak(true, $cutoffMargin);
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
		
		$pdf->SetFont($fontName,'', 10 /*20*/);
		$pdf->SetTextColor(32);
		if ($textColor != null && $textColor != "") {
				$arr = explode(",", $textColor);
				$red = $arr[0];
				$green = $arr[1];
				$blue = $arr[2];
				$pdf->SetTextColor($red, $green, $blue);
		
		}
	
		// print info header
		$width = $pdf->w;
		$height = $pdf->h;
		
		$currY = 35;
		$leftX = 10;
		$pdf->SetX($currX);
		$pdf->SetY($currY);
		$infoTitle = "LUNCH ORDER FORM";
		$pdf->SetFont($fontName,'B',16);
		$pdf->SetXY(0, $currY);
		$pdf->Cell(0, 0, $infoTitle, 0, 0, 'C', false);

		// left column headers
		$valueX = 30;
		$currY = $currY + 8;
		$pdf->SetFont($fontName,'B',12);
		$pdf->SetXY($leftX, $currY);
		$pdf->Cell(0, 0, "Name:", 0, 0, "L", false);

		$pdf->SetXY($valueX, $currY);
		$pdf->SetFont($fontName,'',12);
		$pdf->Cell(20, 0, $studentRow["name"], 0,0, "L", false);
		
		$currY = $currY + 5;
		$pdf->SetFont($fontName,'B',12);
		$pdf->SetXY($leftX, $currY);
		$pdf->Cell(0, 0, "School:", 0, 0, "L", false);

		$pdf->SetXY($valueX, $currY);
		$pdf->SetFont($fontName,'',12);
		$pdf->Cell(40, 0, substr($schoolRow["name"],0,25), 0,0, "L", false);

		$currY = $currY + 5;
		$pdf->SetFont($fontName,'B',12);
		$pdf->SetXY($leftX, $currY);
		$pdf->Cell(0, 0, "Order For:", 0, 0, "L", false);

		$pdf->SetXY($valueX, $currY);
		$pdf->SetFont($fontName,'',12);
		$pdf->Cell(20, 0, $weekDays, 0,0, "L", false);


		// right column headers
		$currY = 35;
		$rightX = 80;
		$valueX = 95;

		$currY = $currY + 8;
		$pdf->SetFont($fontName,'B',12);
		$pdf->SetXY($rightX, $currY);
		if ($studentModePrint)
			$pdf->Cell(0, 0, "Class:", 0, 0, "L", false);

		$pdf->SetXY($valueX, $currY);
		$pdf->SetFont($fontName,'B',12);
		if ($studentModePrint)
			$pdf->Cell(20, 0, $classRow["name"], 0,0, "L", false);
		
		$currY = $currY + 5;
		$pdf->SetFont($fontName,'B',12);
		$pdf->SetXY($rightX, $currY);
		$pdf->Cell(0, 0, "Total Price:", 0, 0, "L", false);

		$pdf->SetXY($valueX, $currY);
		$pdf->SetFont($fontName,'',12);
		$pdf->Cell(20, 0, "", 0,0, "L", false);
		
		$currY = $currY + 5;
		$pdf->SetFont($fontName,'B',12);
		$pdf->SetXY($rightX, $currY);
		$pdf->Cell(0, 0, "Order Date:", 0, 0, "L", false);

		$pdf->SetXY($valueX, $currY);
		$pdf->SetFont($fontName,'',12);
		$pdf->Cell(20, 0, "", 0,0, "L", false);
		

		// school image
		schoolImage($g_docRoot . "schools/files/" . $schoolRow["image"]);
		

		// left column items - recess morning
		$leftX = 10;
		$priceX = 64.1;
		$currY = $currY + 20;
		$itemStartRow = $currY;
		$pdf->SetFont($fontName,'B', 8 /*14*/);
		$pdf->SetXY($leftX, $currY);
		///$pdf->SetFillColor(171,127,104);
		$pdf->SetFillColor(240,252,3);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell($pdf->w / 2.5, 10, "Recess Morning Order", 0, 0, "L", true);

		// item /price heading
		$currY = $currY + 15;
		$pdf->SetFont($fontName,'B', 8 /*12*/);
		$pdf->SetXY($leftX, $currY);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(50,0, "Item", 0, 0, "L", false);
		
		$pdf->SetXY($priceX, $currY);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(30,0, "Price", 0, 0, "R", false);


		// remove items which are not in recess
		$rows2 = array();
		for($i=0; $i < count($productRows); $i++) {
			$productRow = $productRows[$i];
			if ($productRow["flag_recess"] == 1) {
				$rows2 [] = $productRow;
			}
		}
		
		$productRows = $rows2;
		
		$currY = $currY + 3;
		foreach($catRows as $catRow) {
				// skip category if it is not for this meal type
			if ($catRow["meal_type"] != "RECESS")
			  continue;

		    $items = $catRow["items"];
			$arrItems = explode("," , $items);
			
		  // remove items which are not in this category
		  $irows = array();
		  for($i = 0; $i < count($productRows); $i++) {
		  	 $productRow = $productRows[$i];
			 if (in_array($productRow["ID"], $arrItems)) {
			 	$irows[] = $productRow;
			 	}
		  }
		  if (count($irows) == 0) {
		  	continue;
		  }

		  // category heading
		  if (false) {
		  $pdf->SetFont($fontName,'B', 8 /*10*/);
		  $pdf->SetXY($leftX, $currY);
		  //$pdf->SetFillColor(84,180,0);
		  $pdf->setFillColor(255,255,0);
		  //$pdf->SetTextColor(255,255,255);
	  	  $pdf->SetTextColor(5, 181, 234);
		  $pdf->Cell($pdf->w / 2.5, 6, $catRow["name"], 0, 0, "L", true);
		  }

	  	  // sort items
		  $sorted = false;
		  while (!$sorted) {
		  	$sorted  = true;
			for($i=0; $i < count($irows)-1; $i++) {
				$first = $irows[$i];
				$second = $irows[$i+1];
				if (strtolower($first["name"]) > strtolower($second["name"])) {
					$irows[$i] = $second;
					$irows[$i+1] = $first;
					$sorted = false;
				}

			}
		  }
		  
		  $evenRow = true;
		  // items
		  $firstItem = true;
		  foreach($irows as $irow) {
	
		  	  if ($firstItem) {
			  	  $currY = $currY + 0;
				  $firstItem = false;
			  } else {
			  	  $currY = $currY + 6;
			  }
			  $pdf->SetFont($fontName,'', 8 /*10*/);
			  $pdf->SetXY($leftX, $currY);
			  $pdf->SetTextColor(0,0,0);
			  if ($evenRow) 
			  	$pdf->SetFillColor(240,240,240);
			  else
			  	$pdf->SetFillColor(230, 230,230);
			  $evenRow = !$evenRow;
			  $pdf->SetAutoPageBreak(false, $cutoffMargin);
			  $pdf->Cell(60, 6, $irow["name"], 0, 0, "L", true);
			  
			  $pdf->SetXY($priceX, $currY);
			  $pdf->Cell(30, 6,"$ " . number_format($irow["price"],2) . " [     ]", 0, 0, "R", true);
			  $pdf->SetAutoPageBreak(1, $cutoffMargin);
		  }
		  $currY = $currY + 6;

		
		} // foreach($catRows as $catRow) {

		
		///////////////////
		if (false) {
		// meal deal rows
		$leftX = 10;
		$priceX = 64.1;
		$currY = $currY + 10;

		
		$pdf->SetFont($fontName,'B', 8 /*14*/);
		$pdf->SetXY($leftX, $currY);
		//$pdf->SetFillColor(117,152,90);
		$pdf->SetFillColor(255,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell($pdf->w / 2.5, 10, "Meal Deal", 0, 0, "L", true);


		// item /price heading
		$currY = $currY + 14;

		$pdf->SetFont($fontName,'B',8 /*12*/);
		$pdf->SetXY($leftX, $currY);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(50,0, "Item", 0, 0, "L", false);
		
		$pdf->SetXY($priceX, $currY);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(30,0, "Price", 0, 0, "R", false);


	    $evenRow = true;
		$firstItem = true;
		foreach($mdItems as $irow) {
		
		  	  if ($firstItem) {
			  	  $currY = $currY + 3;
				  $firstItem = false;
			  } else {
			  	  $currY = $currY + 6;
			  }
				  
			  $pdf->SetFont($fontName,'', 8 /*10*/);
			  $pdf->SetXY($leftX, $currY);
			  $pdf->SetTextColor(0,0,0);
			  if ($evenRow) 
			  	$pdf->SetFillColor(240,240,240);
			  else
			  	$pdf->SetFillColor(230, 230,230);
			  $evenRow = !$evenRow;
			  
			  $pdf->Cell(60, 6, $irow["name"], 0, 0, "L", true);
			  
			  $pdf->SetXY($priceX, $currY);
			  $pdf->Cell(30, 6,"$ " . number_format($irow["price"],2), 0, 0, "R", true);
	

		}

		}
		/////////////////
		$pdf->SetPage(1);

		$printingLunch = true;
		
		// right column items - lunch
		$leftX = 110;
		$priceX = 154.1;
		$currY = $itemStartRow;
		$pdf->SetXY(0, $currY);
		$pdf->SetFont($fontName,'B', 8 /*14*/);
		$pdf->SetXY($leftX, $currY);
		//$pdf->SetFillColor(167,161,117);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(0,174,232);
		$pdf->Cell($pdf->w / 2.5, 10, "Lunch Time Order", 0, 0, "L", true);

		
		// item /price heading
		$currY = $currY + 15;
		$pdf->SetFont($fontName,'B',8 /*12*/);
		$pdf->SetXY($leftX, $currY);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(50,0, "Item", 0, 0, "L", false);
		
		$pdf->SetXY($priceX, $currY);
		$pdf->SetTextColor(0,0,0);
		$pdf->Cell(40,0, "Price", 0, 0, "R", false);


		// remove items which are not in lunch
		$productRows = $mainProductRows;
		$rows3 = array();
		for($i=0; $i < count($productRows); $i++) {
			$productRow = $productRows[$i];
			if ($productRow["flag_lunch"] == 1) {
				$rows3 [] = $productRow;
			}
		}
		$productRows = $rows3;
		
		$currY = $currY + 3;
		foreach($catRows as $catRow) {
			
			// skip category if it is not for this meal type
			if ($catRow["meal_type"] != "LUNCH")
			  continue;

		    $items = $catRow["items"];
			$arrItems = explode("," , $items);
			
		  // remove items which are not in this category
		  $irows = array();
		  for($i = 0; $i < count($productRows); $i++) {
		  	 $productRow = $productRows[$i];
			 if (in_array($productRow["ID"], $arrItems)) {
			 	$irows[] = $productRow;
			 }
		  }
		  if (count($irows) == 0) {
		  	continue;
		  }

		  // category heading
		  if (false) {
		  $pdf->SetFont($fontName,'B', 8 /*10*/);
		  $pdf->SetXY($leftX, $currY);
		  $pdf->SetFillColor(5, 181, 234);
		  $pdf->setTextColor(255,255,0);
		  //echo("cat y=" . $currY . "<br>");
		  if ($currY > 280) {
		    //echo("changing page to 2<br>");
			$pdf->SetPage(2);
			$currY = 10;
			$pdf->SetXY($leftX, $currY);
			//echo("setting y to " . $currY . "<br>");

  			$pdf->SetFont($fontName,'B', 8 /*10*/);
		    $pdf->SetFillColor(5, 181, 234);
		    $pdf->setTextColor(255,255,0);
			
		   }

		  $pdf->Cell($pdf->w / 2.5, 8, $catRow["name"], 0, 0, "L", true);
		  }



 		 // sort items
		  $sorted = false;
		  while (!$sorted) {
		  	$sorted  = true;
			for($i=0; $i < count($irows)-1; $i++) {
				$first = $irows[$i];
				$second = $irows[$i+1];
				if (strtolower($first["name"]) > strtolower($second["name"])) {
					$irows[$i] = $second;
					$irows[$i+1] = $first;
					$sorted = false;
				}

			}
		  }


		  $evenRow = true;
		  // items
		  $firstItem = true;
		  foreach($irows as $irow) {
	
		  	  if ($firstItem) {
			  	  $currY = $currY + 0;
				  $firstItem = false;
			  } else {
			  	  $currY = $currY + 6;
			  }
			  $pdf->SetFont($fontName,'',8 /*10*/);
			  $pdf->SetXY($leftX, $currY);
			  $pdf->SetTextColor(0,0,0);
			  if ($evenRow) 
			  	$pdf->SetFillColor(240,240,240);
			  else
			  	$pdf->SetFillColor(230, 230,230);
			  $evenRow = !$evenRow;
		  	  if ($currY > 260) {
				//echo("changing page to 2<br>");
				$pdf->SetPage(2);
				$currY = 10;
				$pdf->SetXY($leftX, $currY);
				//echo("setting y to " . $currY . "<br>");

 				$pdf->SetFont($fontName,'', 8 /*10*/);
			    $pdf->SetXY($leftX, $currY);
			    $pdf->SetTextColor(0,0,0);
			    if ($evenRow) 
			  	  $pdf->SetFillColor(240,240,240);
			    else
			   	 $pdf->SetFillColor(230, 230,230);
				
			   }

			  
			  //echo("y1=" . $currY . "<br>");
			  $pdf->Cell(60, 6, $irow["name"], 0, 0, "L", true);
			  //echo("y2=" . $currY . "<br>");
			  $pdf->SetXY($priceX, $currY);
			  $pdf->Cell(40, 6,"$ " . number_format($irow["price"],2) . " [     ]", 0, 0, "R", true);
			  //$pdf->SetAutoPageBreak(true, $cutoffMargin);


		  }
		  $currY = $currY + 6;

		
		} // foreach($catRows as $catRow) {

	

		// go to last page 
		$pdf->SetPage($pages);

		// generate pdf file
		$filename = $g_docRoot . "output/school-" . $schoolRow["ID"]. ".pdf";
		$pdf->Output($filename,'F');

		exit($schoolRow["ID"]);

	function schoolImage($imageFile) {
		global $g_docRoot,$pdf;
			
		$f = fopen($imageFile, "r");
		if ($f) {
			fclose($f);
			$extension = strtolower(substr($imageFile, strlen($imageFile)-3, 3));
				// create  image file
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
			$pdf->Image($imageFile,$pdf->w - 50, 30, null,30);
		}
}



?>
