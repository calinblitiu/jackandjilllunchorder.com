<?php
require_once("generic-table.php");
class SubsWalletPayments extends GenericTable {

	var $mTable = "j_subs_wallet_payments";

	/**
	 * Get count of all rows for a member
	 * @param int $memberId - memberid
	 * @return int $retVal count of rows
	 */
	function getCountForMember($memberId) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where member_id= :member_id";
	
		$this->mDb->query($sql);
		$this->mDb->bind(":member_id", $memberId);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Get total for a member
	 * @param int $memberId - memberid
	 * @return array $row
	 */
	function getTotalForMember($memberId) {

		$sql ="SELECT sum(amount) as total from ". $this->mTable . " where member_id = :member_id";

		$this->mDb->query($sql);

		$this->mDb->bind(":member_id", $memberId);

		$row = $this->mDb->single();
		
		$this->mError = $this->mDb->getLastError();
		
		return $row;

	}


	/**
	 * Get total purchases for a subscription id
	 * @param int $subsId - subsid
	 * @return array $row
	 */
	function getTotalForSubscription($subsId) {

		$sql ="SELECT sum(amount) as total from " . $this->mTable . " where subscription_id = :subscription_id and used_credits=1";

		$this->mDb->query($sql);

		$this->mDb->bind(":subscription_id", $subsId);

		$row = $this->mDb->single();
		
		$this->mError = $this->mDb->getLastError();
		
		return $row;

	}


	/**
	 * Get all rows for a member
	 * @param int $memberId - memberid
 	 * @param int $row - starting row
	 * @param int rowsPerPage - rows to fetch

	 * @return array $rows 
	 */
	function getRowsForMember($memberId,  $startFrom, $rowsPerPage) {

		$sql ="SELECT * from " . $this->mTable . " where member_id = :member_id" .
					" order by date desc";
		$sql .=  " limit " . $startFrom . ", " . $rowsPerPage;

		$this->mDb->query($sql);
		$this->mDb->bind(":member_id", $memberId);
		$rows = $this->mDb->resultset();
		$this->mError = $this->mDb->getLastError();
		
		return $rows;

	}

	/**
	 * Get count of all rows for a student
	 * @param int $studentid
	 * @return int $retVal count of rows
	 */
	function getCountForStudent($studentId) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where student_id= :student_id";
	
		$this->mDb->query($sql);
		$this->mDb->bind(":student_id", $studentId);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}




}

?>
