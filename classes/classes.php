<?php
require_once($g_docRoot . "classes/generic-table.php");

class Classes extends GenericTable {

	var $mTable = "j_classes";


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
	function getListForASchool($schoolId, $startFrom, $rowsPerPage, $sort) {
		
		$sql ="SELECT * from " . $this->mTable . " where school_id=:school_id";
		$sql .= " order by ID";

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);

 		$this->mDb->bind(":school_id", $schoolId);
		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}


	/**
	 * Get  enabled class list for a school
	 * @param int $schoolId
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getEnabledListForASchool($schoolId, $startFrom, $rowsPerPage, $sort) {
		
		$sql ="SELECT * from " . $this->mTable . " where school_id=:school_id and flag=1";
		$sql .= " order by ID";

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);

 		$this->mDb->bind(":school_id", $schoolId);
		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}

}
?>
