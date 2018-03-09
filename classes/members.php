<?php
require_once("generic-table.php");
class Members extends GenericTable {

	var $mTable = "j_users";


	/**
	 * Get count of all rows between two dates
	 * @param string $from starting date
	 * @param string $till ending date
	 * @return int $retVal count of rows
	 */
	function getCountBetweenDates($from=null, $till=null) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from j_users ";
		if ($from != null && $till != null) {
			$sql .= " where signup_date >= :from and signup_date <= :till";
		}
		$this->mDb->query($sql);
		if ($from != null && $till != null) {
			$this->mDb->bind(":from", $from);
			$this->mDb->bind(":till", $till);
		}

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Authenticate a login 
	 * @param string $emailid
	 * @param string $pwd password hash
	 * @return array $row data row if exists else no data
	 */
	 
	function authenticate($emailid, $pwd) {
	
		$this->mError = null;
		$sql ="SELECT *  from " . $this->mTable ;
		$sql .= " where emailid =:emailid and pwd=:pwd";
		$this->mDb->query($sql);
		$this->mDb->bind(":emailid", $emailid);
		$this->mDb->bind(":pwd", $pwd);

		$row = $this->mDb->single();

		return $row;
	}

	/**
	 * Check if email exists
	 * @param string $email
	 * @return array $row data row if exists else no data
	 */
	 
	function emailExists($email) {
	
		$this->mError = null;
		$sql ="SELECT *  from  " . $this->mTable;
		$sql .= " where emailid=:emailid";
		
		$this->mDb->query($sql);
		$this->mDb->bind(":emailid", $email);
		$row = $this->mDb->single();

		return $row;

	}


	/**
	 * Check if mobile exists
	 * @param string $mobile
	 * @return array $row data row if exists else no data
	 */
	 
	function mobileExists($mobile) {
	
		$this->mError = null;
		$sql ="SELECT *  from  " . $this->mTable;
		$sql .= " where mobile=:mobile";
		
		$this->mDb->query($sql);
		$this->mDb->bind(":mobile", $mobile);
		$row = $this->mDb->single();

		return $row;

	}



	/**
	 * Verify code
	 * @param string $otp
	 * @return array $row data row if exists else no data
	 */
	 
	function verifyCode($otp) {
	
		$this->mError = null;
		$sql ="SELECT *  from  " . $this->mTable;
		$sql .= " where verify_code=:verify_code";
		
		$this->mDb->query($sql);
		$this->mDb->bind(":verify_code", $otp);
		$row = $this->mDb->single();

		return $row;

	}



	/**
	 * Get count of member list
	 * @param string $name 
	 * @param string $email
	 * @return int $retVal count of rows
	 */
	function getMemberListCount($name, $email) {
		$retVal = 0;
		$and = " where ";
		$sql ="SELECT count(*) as total from " . $this->mTable;
		if ($name != null && $name != null) {
			$sql .= " where name like '%'" . $name . "%'";
			$and = " and ";
		}
		if ($email != null && $email != "")
			$sql .= $and . " email =:email";

		$this->mDb->query($sql);
		if ($email != null && $email != null) {
			$this->mDb->bind(":email", $email);
		}

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}

	/**
	 * Get member list 
	 * @param string $name 
	 * @param string $email
	 * @param string sort
	 * @param int $row - starting row
	 * @param int rowsPerPage - rows to fetch
	 * @return array $rows row of data
	 */
	function getMemberList($name, $email, $sort, $startFrom, $rowsPerPage) {
		$and = " where ";

		$arrSort = ["iddesc"=>"ID desc", "idasc"=>"id asc",
				 "namedesc"=>"account_name desc", "nameasc"=>"account_name asc",
				 "emaildesc"=>"email desc", "emailasc"=>"email asc",
				 "signupdesc"=>"signup_date desc", "signupasc"=>"signup_date asc",
				 "itemsdesc"=>"items_count desc", "itemsasc"=>"items_count asc",
				 "salesdesc"=>"total_sales desc", "salesasc"=>"total_sales asc"];
		$sort = $arrSort[$sort];

		$sql ="SELECT * from  " . $this->mTable;
		if ($name != null && $name != null) {
			$sql .= " where lower(account_name) like lower('%" . $name . "%')";
			$and = " and ";
		}
		if ($email != null && $email != "")
			$sql .= $and . " lower(email) = lower(:email)";

		$sql .= " order by " . $sort;
		
		$this->mDb->query($sql);
		if ($email != null && $email != null) {
			$this->mDb->bind(":email", $email);
		}
		$rows = $this->mDb->resultset();
		$this->mError = $this->mDb->getLastError();
		return $rows;

	}


	/**
	 * Get count of all rows for a search
	 * @param string $fname
	 * @param string $lname
	 * @param string email
	 * @return int $retVal count of rows
	 */
	function getCountForSearch($fname, $lname, $email) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where 1";
		if ($fname != null && $fname != "")
			$sql .= " and fname like :fname";
	    if ($lname != null && $lname != "")
			$sql .= " and lname like :lname";
		if ($email != null && $email != "")
			$sql .= " and emailid = :email";

		$this->mDb->query($sql);
		if ($fname != null && $fname != "")
			$this->mDb->bind(":fname", "%" . $fname . "%");

	    if ($lname != null && $lname != "")
			$this->mDb->bind(":lname", "%" . $lname . "%");

		if ($email != null && $email != "")
			$this->mDb->bind(":email", $email);

		
		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Get member list 
 	 * @param string $fname
	 * @param string $lname
	 * @param string email
	 * @param string sort
	 * @param int $row - starting row
	 * @param int rowsPerPage - rows to fetch
	 * @return array $rows row of data
	 */
	function getListForSearch($fname, $lname, $email, $sort, $startFrom, $rowsPerPage) {
		$and = " where ";

		$arrSort = ["iddesc"=>"ID desc", "idasc"=>"id asc",
				 "fnamedesc"=>"fname desc", "fnameasc"=>"fname asc",
				 "lnamedesc"=>"lname desc", "lnameasc"=>"lname asc",
				 "emaildesc"=>"email desc", "emailasc"=>"email asc",
				 "signupdesc"=>"signup_date desc", "signupasc"=>"signup_date asc"
				 ];
		$sort = $arrSort[$sort];

		$sql ="SELECT * from  " . $this->mTable . " where 1";
		if ($fname != null && $fname != "")
			$sql .= " and fname like :fname";
	    if ($lname != null && $lname != "")
			$sql .= " and lname like :lname";
		if ($email != null && $email != "")
			$sql .= " and emailid = :email";

		$sql .= " order by " . $sort;
		$sql .= " limit " . $startFrom . "," . $rowsPerPage;

		$this->mDb->query($sql);
		if ($fname != null && $fname != "")
			$this->mDb->bind(":fname", "%" . $fname . "%");

	    if ($lname != null && $lname != "")
			$this->mDb->bind(":lname", "%" . $lname . "%");

		if ($email != null && $email != "")
			$this->mDb->bind(":email", $email);
		
		$rows = $this->mDb->resultset();
		$this->mError = $this->mDb->getLastError();
		return $rows;

	}


	


}
?>
