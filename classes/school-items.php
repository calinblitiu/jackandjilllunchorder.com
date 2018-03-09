<?php
require_once($g_docRoot . "classes/generic-table.php");

class SchoolItems extends GenericTable {

	var $mTable = "j_school_items";


	/**
	 * Get count of all rows for a school
	 * @param int productId
	 * @return int $retVal count of rows
	 */
	function getCountForASchool($schoolId) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where school_id=:school_id ";
		$this->mDb->query($sql);

 		$this->mDb->bind(":school_id", $schoolId);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Get  list for a school
	 * @param int $schoolId
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getListForASchool($schoolId, $startFrom, $rowsPerPage) {
		
		$sql ="SELECT " . $this->mTable . ".*, j_products.name as productname from " . $this->mTable . " inner join j_products on " . $this->mTable .".product_id = j_products.ID where school_id=:school_id";
		$sql .= " order by ID";

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);

 		$this->mDb->bind(":school_id", $schoolId);
		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

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
