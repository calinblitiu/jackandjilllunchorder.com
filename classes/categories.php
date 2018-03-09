<?php
require_once($g_docRoot . "classes/generic-table.php");
class Categories extends GenericTable {

	var $mTable = "j_categories";


	/**
	 * Get count of all rows 
	 * @return int $retVal count of rows
	 */
	function getCount() {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where ID > 0 ";
		$this->mDb->query($sql);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Get  list 
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getList($startFrom, $rowsPerPage, $sort) {
		
		$sql ="SELECT * from " . $this->mTable . " where ID > 0";
		$sql .= " order by " . $sort;

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);


		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}

	/**
	 * Get categories in which an item is present
	 * @param int $productId
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getCatsForItem($productId, $startFrom, $rowsPerPage, $sort) {
		
		$sql ="SELECT * from " . $this->mTable . " where items like :items order by ID";

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);
		$this->mDb->bind(":items", "%" . $productId . "%");

		$rows = $this->mDb->resultset();
		
		// remove false positives
		$rows2 = array();
		foreach($rows as $row) {
			$arr = explode("," , $row["items"]);
			if (in_array($productId, $arr)) {
				$rows2[] = $row;
			}
		}

		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}


	/**
	 * Get row by name 
	 * @return array $row
	 */
	function getRowByName($name) {
		$retVal = 0;
		
		$sql ="SELECT * from " . $this->mTable . " where name=:name";
		$this->mDb->query($sql);
		$this->mDb->bind(":name", $name);

		$row = $this->mDb->single();

		
		$this->mError = $this->mDb->getLastError();
		return $row;

	}

}
?>
