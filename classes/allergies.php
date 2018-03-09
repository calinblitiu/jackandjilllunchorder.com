<?php
require_once($g_docRoot . "classes/generic-table.php");

class Allergies extends GenericTable {

	var $mTable = "j_allergies";


	/**
	 * Get count of all rows for a product
	 * @param int productId
	 * @return int $retVal count of rows
	 */
	function getCountForAProduct($productId) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where product_id=:product_id ";
		$this->mDb->query($sql);

 		$this->mDb->bind(":product_id", $productId);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Get  list for a product
	 * @param int $productId
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getListForAProduct($productId, $startFrom, $rowsPerPage, $sort) {
		
		$sql ="SELECT * from " . $this->mTable . " where product_id=:product_id";
		$sql .= " order by ID";

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);

 		$this->mDb->bind(":product_id", $productId);
		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}


}
?>
