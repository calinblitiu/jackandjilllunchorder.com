<?php
require_once($g_docRoot . "classes/generic-table.php");

class Students extends GenericTable {

	var $mTable = "j_student";


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
		$arrSort = ["date_desc"=>"date_added desc", "date_asc"=>"date_added asc",
					"name_desc"=>$this->mTable .".name desc", "name_asc"=>$this->mTable . ".name asc",
					"schoolname_asc"=>"j_schools.name asc", "schoolname_desc"=>"j_schools.name desc"];
		$sort = $arrSort[$sort];
		
		$sql ="SELECT " . $this->mTable . ".* from " . $this->mTable . "  inner join j_schools on " . $this->mTable . ".school_id=j_schools.ID  where user_id=:user_id";
		$sql .= " order by " . $sort;

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);
 		$this->mDb->bind(":user_id", $userId);
		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}

	/**
	 * Get count of all rows for a user with search
	 * @param int $userId
	 * @param string $name 
	 * @return int $retVal count of rows
	 */
	function getCountForAUserWithSearch($userId, $name) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where user_id=:user_id and name like :name";
		$this->mDb->query($sql);

 		$this->mDb->bind(":user_id", $userId);
		$this->mDb->bind(":name", "%" . $name . "%");

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}



	/**
	 * Get  list for a user with search
	 * @param int $userId
	 * @param int $userId
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getListForAUserWithSearch($userId, $name, $startFrom, $rowsPerPage, $sort) {
		$arrSort = ["date_desc"=>"date_added desc", "date_asc"=>"date_added asc",
					"name_desc"=>$this->mTable .".name desc", "name_asc"=>$this->mTable . ".name asc"];
		$sort = $arrSort[$sort];
		
		$sql ="SELECT " . $this->mTable . ".* from " . $this->mTable . "  inner join j_schools on " . $this->mTable . ".school_id=j_schools.ID  where user_id=:user_id and " . $this->mTable . ".name like :name";
		$sql .= " order by " . $sort;
		$sql .= " limit " . $startFrom . "," . $rowsPerPage;

		$this->mDb->query($sql);
 		$this->mDb->bind(":user_id", $userId);
		$this->mDb->bind(":name", "%" . $name . "%");

		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}


	/**
	 * Get count of all rows for a school
	 * @param int $schoolId
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
	 * Get count of all rows for a class
	 * @param int $classId
	 * @return int $retVal count of rows
	 */
	function getCountForAClass($classId) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where class_id=:class_id ";
		$this->mDb->query($sql);

 		$this->mDb->bind(":class_id", $classId);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}

}
?>
