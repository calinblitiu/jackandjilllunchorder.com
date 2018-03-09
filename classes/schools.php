<?php
require_once($g_docRoot . "classes/generic-table.php");
class Schools extends GenericTable {

	var $mTable = "j_schools";


	/**
	 * Get count of all rows 
	 * @param string $name
	 * @param int global
	 * @return int $retVal count of rows
	 */
	function getCount($name) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where ID > 0 ";
		if ($name != null && $name != "")
			$sql .= " and name like :name";
	 		$this->mDb->query($sql);

		if ($name != null && $name != "")
	 		$this->mDb->bind(":name", "%" . $name . "%");
	
		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Get  list 
	 * @param string $name
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getList($name, $startFrom, $rowsPerPage, $sort) {
		$arrSort = ["date_desc"=>"date_added desc", "date_asc"=>"date_added asc", 
			    "name_desc"=>"name desc", "name_asc"=>"name asc"];
		
		$sql ="SELECT * from " . $this->mTable . " where ID > 0";
		if ($name != null && $name != "")
			$sql .= " and name like :name";
		
		if ($sort == null || $sort == "")
			$sql .= " order by date_added desc ";
		else
			$sql .= " order by " . $arrSort[$sort];

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);

		if ($name != null && $name != "")
	 		$this->mDb->bind(":name", "%" . $name . "%");
	

		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}


}
?>
