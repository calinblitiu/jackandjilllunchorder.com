<?php
require_once($g_docRoot . "classes/generic-table.php");
class AllergiesMaster extends GenericTable {

	var $mTable = "j_allergiesmaster";


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
	 * @return array $rows row of data
	 */
	function getList($startFrom, $rowsPerPage, $sort) {
		
		$sql ="SELECT * from " . $this->mTable . " where ID > 0";
		$sql .= " order by name ";

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);

		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}


	/**
	 * Get  list of enabled items
	 * @return array $rows row of data
	 */
	function getEnabledList($startFrom, $rowsPerPage) {
		
		$sql ="SELECT * from " . $this->mTable . " where enabled = 1";
		$sql .= " order by name ";

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);

		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}

}
?>
