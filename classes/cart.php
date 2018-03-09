<?php
require_once($g_docRoot . "classes/generic-table.php");

class Cart extends GenericTable {

	var $mTable = "j_cart";


	/**
	 * Get count of all rows for a user
	 * @param int userId
	 * @return int $retVal count of rows
	 */
	function getCountForAUser($userId) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where user_id=:user_id ";
		$this->mDb->query($sql);

 		$this->mDb->bind(":user_id", $userId);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Get  list for a user
	 * @param int $userId
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getListForAUser($userId, $startFrom, $rowsPerPage, $sort) {
		$arrSort = ["date_desc"=>"date_added desc", "date_asc"=>"date_added asc"
					];
		$sort = $arrSort[$sort];
		
		$sql ="SELECT " . $this->mTable . ".*, j_products.name as productname, j_products.image as image from " . $this->mTable . "  inner join j_products on " . $this->mTable . ".product_id=j_products.ID inner join j_student on " . $this->mTable .".student_id = j_student.ID  where " . $this->mTable . ".user_id=:user_id";
		$sql .= " order by " . $sort;

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);
 		$this->mDb->bind(":user_id", $userId);
		$rows = $this->mDb->resultset();

		// special code to add mealdeal row into resultset as the inner joins wont work
		$sql = " select * from " . $this->mTable . " where " . $this->mTable . ".user_id=:user_id and product_id=" . MEAL_DEAL_ITEM_DISPLAY_ID . " limit 0,1";
		$this->mDb->query($sql);
		$this->mDb->bind(":user_id", $userId);
		$row = $this->mDb->single();
		if ($row) {
			$rows[] = $row;
		}

		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}




	/**
	 * Get  list for a user , grouped by meal type
	 * @param int $userId
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @return array $rows row of data
	 */
	function getGroupedListForAUser($userId, $startFrom, $rowsPerPage) {
		
		$sql ="SELECT " . $this->mTable . ".*, j_products.name as productname, j_products.image as image from " . $this->mTable . "  inner join j_products on " . $this->mTable . ".product_id=j_products.ID inner join j_student on " . $this->mTable .".student_id = j_student.ID  where " . $this->mTable . ".user_id=:user_id";
		$sql .= " order by meal_type desc";

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);
 		$this->mDb->bind(":user_id", $userId);
		$rows = $this->mDb->resultset();

		// special code to add mealdeal row into resultset as the inner joins wont work
		$sql = " select * from " . $this->mTable . " where " . $this->mTable . ".user_id=:user_id and product_id=" . MEAL_DEAL_ITEM_DISPLAY_ID . " limit 0,1";
		$this->mDb->query($sql);
		$this->mDb->bind(":user_id", $userId);
		$row = $this->mDb->single();
		if ($row) {
			if (count($rows) > 0) {
				$wasInserted = false;
				// insert it in the correct place to keep the meal deal sequence
				for($i = 0; $i < count($rows); $i++) {
					$trow = $rows[$i];
					if ($trow["meal_type"] == $row["meal_type"]) {
						$topPart = array_slice($rows, 0, $i);
						$bottomPart = array_slice($rows, $i);
						$rows = $topPart;
						$rows[] = $row;
						foreach($bottomPart as $bottom) {
							$rows[] = $bottom;
						}
						$wasInserted = true;
						break;
					}
				}
				if (!$wasInserted) {
					$rows[] = $row;
				}
			} else  {
				$rows[] = $row;
			}
		}

		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}


	/**
	 * Check if a cart entry exists
	 * @param int $userId
	 * @param int $studentId
	 * @param int $productId
	 * @param date $orderDate
	 * @return array $row 
	 */
	function cartEntryExists($userId, $studentId, $productId, $orderDate) {
		
		$sql ="SELECT * from " . $this->mTable . " where user_id=:user_id and student_id=:student_id and product_id=:product_id and order_date=:order_date";
		$this->mDb->query($sql);
	
 		$this->mDb->bind(":user_id", $userId);
		$this->mDb->bind(":student_id", $studentId);
		$this->mDb->bind(":product_id", $productId);
		$this->mDb->bind(":order_date", $orderDate);

		$row = $this->mDb->single();
		
		$this->mError = $this->mDb->getLastError();
		

		return $row;

	}


	/**
	 * Check if a cart entry exists with mealtype
	 * @param int $userId
	 * @param int $studentId
	 * @param int $productId
	 * @param date $orderDate
	 * @return array $row 
	 */
	function cartEntryExistsWithMealType($userId, $studentId, $productId, $orderDate, $mealType) {
		
		$sql ="SELECT * from " . $this->mTable . " where user_id=:user_id and student_id=:student_id and product_id=:product_id and order_date=:order_date and meal_type=:meal_type";
		$this->mDb->query($sql);
	
 		$this->mDb->bind(":user_id", $userId);
		$this->mDb->bind(":student_id", $studentId);
		$this->mDb->bind(":product_id", $productId);
		$this->mDb->bind(":order_date", $orderDate);
		$this->mDb->bind(":meal_type", $mealType);

		$row = $this->mDb->single();
		
		$this->mError = $this->mDb->getLastError();
		

		return $row;

	}

	/**
	 * Get count of all rows for a product
	 * @param int $productid
	 * @return int $retVal count of rows
	 */
	function getCountForProduct($productId) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where product_id= :product_id";
	
		$this->mDb->query($sql);
		$this->mDb->bind(":product_id", $productId);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}



}
?>
