<?php
require_once("generic-table.php");
class Subscriptions extends GenericTable {

	var $mTable = "j_subscriptions";

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
	 * Get total subscriptions for a member
	 * @param int $memberId - memberid
	 * @return array $row
	 */
	function getTotalSubscriptionsForMember($memberId) {

		$sql ="SELECT sum(net_total) as total from ". $this->mTable . " where member_id = :member_id";

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
	 * Get count of all rows for a member/product pair
	 * @param int $memberId - memberid
	 * @parm int $productId
	 * @return int $retVal count of rows
	 */
	function getCountForMemberAndProduct($memberId, $productId) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where member_id= :member_id and product_id=:product_id";
	
		$this->mDb->query($sql);
		$this->mDb->bind(":member_id", $memberId);
		$this->mDb->bind(":product_id", $productId);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}

	/**
	 * Get row for a member/txn_id pair
	 * @param int $memberId - memberid
 	 * @param string $txnId
	 * @param int rowsPerPage - rows to fetch

	 * @return array $row
	 */
	function getRowForMemberTxn($memberId,  $txnId) {

		$sql ="SELECT * from " . $this->mTable . " where member_id = :member_id" .
					" and txn_id=:txn_id ";
					

		$this->mDb->query($sql);
		$this->mDb->bind(":member_id", $memberId);
		$this->mDb->bind(":txn_id", $txnId);

		$row = $this->mDb->single();
		$this->mError = $this->mDb->getLastError();
		
		return $row;

	}


	/**
	 * Get count of all rows for admin
	 * @param int $memberId - memberid
	 * @return int $retVal count of rows
	 */
	function getAdminCount($subsId, $dateFrom, $dateTill, $memberId, $cancel_flag) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where ID > 1";

		if ($subsId != null && $subsId != "")
			$sql .= " and ID=:id";
		if ($dateFrom != null && $dateFrom != "")
			$sql .= " and date >=:date_from";
		if ($dateTill != null && $dateTill != "")
			$sql .= " and date <=:date_till";

		if ($memberId != null && $memberId != "")
			$sql .= " and member_id=:member_id";

		if ($cancel_flag != null && $cancel_flag != "")
			$sql .= " and cancel_flag=:cancel_flag";

		$this->mDb->query($sql);

		if ($subsId != null && $subsId != "")
				$this->mDb->bind(":id", $subsId);

		if ($dateFrom != null && $dateFrom != "")
				$this->mDb->bind(":date_from", $dateFrom);

		if ($dateTill != null && $dateTill != "")
			$this->mDb->bind(":date_till", $dateTill);

		if ($memberId != null && $memberId != "")
			$this->mDb->bind(":member_id", $memberId);

		if ($cancel_flag != null && $cancel_flag != "")
		   $this->mDb->bind(":cancel_flag", $cancel_flag);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Get all rows for admin
	 * @param int $memberId - memberid
 	 * @param int $row - starting row
	 * @param int rowsPerPage - rows to fetch

	 * @return array $rows 
	 */
	function getAdminRows($subsId, $dateFrom, $dateTill, $memberId, $cancel_flag,
			$startFrom, $rowsPerPage) {

		$sql ="SELECT * from " . $this->mTable . " where ID > 1";
		if ($subsId != null && $subsId != "")
			$sql .= " and ID=:id";
		if ($dateFrom != null && $dateFrom != "")
			$sql .= " and date >=:date_from";

		if ($dateTill != null && $dateTill != "")
			$sql .= " and date <=:date_till";

		if ($memberId != null && $memberId != "")
			$sql .= " and member_id=:member_id";
	
		if ($cancel_flag != null && $cancel_flag != "")
			$sql .= " and cancel_flag=:cancel_flag";

		
		
		$sql .= " order by date desc";
		$sql .=  " limit " . $startFrom . ", " . $rowsPerPage;
		

		$this->mDb->query($sql);

		if ($subsId != null && $subsId != "")
				$this->mDb->bind(":id", $subsId);

		if ($dateFrom != null && $dateFrom != "")
				$this->mDb->bind(":date_from", $dateFrom);

		if ($dateTill != null && $dateTill != "")
			$this->mDb->bind(":date_till", $dateTill);

		if ($memberId != null && $memberId != "")
			$this->mDb->bind(":member_id", $memberId);

		if ($cancel_flag != null && $cancel_flag != "")
		   $this->mDb->bind(":cancel_flag", $cancel_flag);

		$rows = $this->mDb->resultset();
		$this->mError = $this->mDb->getLastError();
		
		return $rows;

	}


	/**
	 * Get count of all rows between two dates
	 * @param string $from
	 * @param string $till
	 * @return int $retVal count of rows
	 */
	function getCountBetweenDates($from, $till) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where date >=:date_from and date<=:date_till";
	
		$this->mDb->query($sql);
		$this->mDb->bind(":date_from", $from);
		$this->mDb->bind(":date_till", $till);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

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

	/*
	 * Get count of all active rows for a weekday (0 = sunday)
	 * @param int $day
	 * @parm date $starDate
	 * @return int $retVal count of rows
	 */
	function getActiveRowCountForWeekday($day, $startDate) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where ";
		if ($day == 0) 
			$sql .= "day_sun = 1";
		else if ($day == 1)
			$sql .= "day_mon = 1";
		else if ($day == 2)
			$sql .= "day_tue = 1";
		else if ($day == 3)
			$sql .= "day_wed = 1";
		else if ($day == 4)
			$sql .= "day_thu = 1";
		else if ($day == 5)
			$sql .= "day_fri = 1";
		else if ($day == 6)
			$sql .= "day_sat = 1";
				
		if ($startDate != null && $startDate != "")
			$sql .= " and start_date <='" . $startDate . "' and cancelled_flag <> 1";
			
		$this->mDb->query($sql);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}

	
	/**
	 * Get active rows for a weekday (0 = sunday)
 	 * @param int $day
	 * @parm date $starDate
 	 * @param int $row - starting row
	 * @param int rowsPerPage - rows to fetch

	 * @return array $rows 
	 */
	function getActiveRowsForWeekday($day, $startDate, $startFrom, $rowsPerPage) {

		$sql ="SELECT * from " . $this->mTable . " where ";

		if ($day == 0) 
			$sql .= "day_sun = 1";
		else if ($day == 1)
			$sql .= "day_mon = 1";
		else if ($day == 2)
			$sql .= "day_tue = 1";
		else if ($day == 3)
			$sql .= "day_wed = 1";
		else if ($day == 4)
			$sql .= "day_thu = 1";
		else if ($day == 5)
			$sql .= "day_fri = 1";
		else if ($day == 6)
			$sql .= "day_sat = 1";

		if ($startDate != null && $startDate != "")
			$sql .= " and start_date <='" . $startDate . "' and cancelled_flag <> 1";


		$sql .=  " limit " . $startFrom . ", " . $rowsPerPage;
			
		$this->mDb->query($sql);

		$rows = $this->mDb->resultset();
		$this->mError = $this->mDb->getLastError();
		
		return $rows;

	}
	

}

?>
