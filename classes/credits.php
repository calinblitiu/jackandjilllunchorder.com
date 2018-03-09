<?php
require_once("generic-table.php");
class Credits extends GenericTable {

	var $mTable = "j_credits";

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
	 * Get total credits for a member
	 * @param int $memberId - memberid
	 * @return array $row
	 */
	function getTotalCreditsForMember($memberId) {

		$sql ="SELECT sum(amount) as total from " . $this->mTable . " where member_id = :member_id";

		$this->mDb->query($sql);

		$this->mDb->bind(":member_id", $memberId);

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

		$sql ="SELECT * from " . $this->mTable  ." where member_id = :member_id" .
					" order by date desc ";
		$sql .=  " limit " . $startFrom . ", " . $rowsPerPage;
					

		$this->mDb->query($sql);
		$this->mDb->bind(":member_id", $memberId);
		$rows = $this->mDb->resultset();
		$this->mError = $this->mDb->getLastError();
		
		return $rows;

	}

	/**
	 * Get rows for a member and txn id pair
	 * @param int $memberId - memberid
 	 * @param string $txnId - transaction id
	 * @return array $row 
	 */
	function getRowForMemberTxn($memberId,  $txnId) {

		$sql ="SELECT * from " . $this->mTable  . " where member_id = :member_id" .
					" and txn_id=:txn_id ";

		$this->mDb->query($sql);
		$this->mDb->bind(":member_id", $memberId);
		$this->mDb->bind(":txn_id", $txnId);

		$rows = $this->mDb->single();
		$this->mError = $this->mDb->getLastError();
		
		return $rows;

	}

}

?>
