<?php
require_once("generic-table.php");
class Orders extends GenericTable {

	var $mTable = "j_orders";

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
	 * Get total purchases for a member
	 * @param int $memberId - memberid
	 * @return array $row
	 */
	function getTotalPurchasesForMember($memberId) {

		$sql ="SELECT sum(net_total) as total from ". $this->mTable . " where member_id = :member_id";

		$this->mDb->query($sql);

		$this->mDb->bind(":member_id", $memberId);

		$row = $this->mDb->single();
		
		$this->mError = $this->mDb->getLastError();
		
		return $row;

	}


	/**
	 * Get total purchases for a member using credits
	 * @param int $memberId - memberid
	 * @return array $row
	 */
	function getTotalPurchasesForMemberWithCredits($memberId) {

		$sql ="SELECT sum(net_total) as total from " . $this->mTable . " where member_id = :member_id and used_credits=1";

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
	 * Get count of all rows 
	 * @param int $memberId - memberid
	 * @return int $retVal count of rows
	 */
	function getAdminCount($orderId, $dateFrom, $dateTill, $invoiceId, $mealType, $memberId, $status) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where ID > 1";

		if ($orderId != null && $orderId != "")
			$sql .= " and ID=:id";
		if ($dateFrom != null && $dateFrom != "")
			$sql .= " and delivery_date >=:date_from";
		if ($dateTill != null && $dateTill != "")
			$sql .= " and delivery_date <=:date_till";

		if ($invoiceId != null && $invoiceId != "")
			$sql .= " and invoice=:invoice";

		if ($mealType != null && $mealType != "")
			$sql .= " and meal_type=:meal_type";
			
		if ($memberId != null && $memberId != "")
			$sql .= " and member_id=:member_id";

		if ($status != null && $status != "")
			$sql .= " and status=:status";

		$this->mDb->query($sql);

		if ($orderId != null && $orderId != "")
				$this->mDb->bind(":id", $orderId);

		if ($dateFrom != null && $dateFrom != "")
				$this->mDb->bind(":date_from", $dateFrom);

		if ($dateTill != null && $dateTill != "")
			$this->mDb->bind(":date_till", $dateTill);

		if ($invoiceId != null && $invoiceId != "")
			$this->mDb->bind(":invoice", $invoice);

		if ($mealType != null && $mealType != "")
			$this->mDb->bind(":meal_type", $mealType);

		if ($memberId != null && $memberId != "")
			$this->mDb->bind(":member_id", $memberId);

		if ($status != null && $status != "")
		   $this->mDb->bind(":status", $status);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Get all rows for a member
	 * @param int $memberId - memberid
 	 * @param int $row - starting row
	 * @param int rowsPerPage - rows to fetch

	 * @return array $rows 
	 */
	function getAdminRows($orderId, $dateFrom, $dateTill, $invoiceId, $mealType, $memberId, $status,
			$startFrom, $rowsPerPage) {

		$sql ="SELECT * from " . $this->mTable . " where ID > 1";
		if ($orderId != null && $orderId != "")
			$sql .= " and ID=:id";
		if ($dateFrom != null && $dateFrom != "")
			$sql .= " and delivery_date >=:date_from";

		if ($dateTill != null && $dateTill != "")
			$sql .= " and delivery_date <=:date_till";

		if ($invoiceId != null && $invoiceId != "")
			$sql .= " and invoice=:invoice";

		if ($mealType != null && $mealType != "")
			$sql .= " and meal_type=:meal_type";
			
		if ($memberId != null && $memberId != "")
			$sql .= " and member_id=:member_id";
	
		if ($status != null && $status != "")
			$sql .= " and status=:status";
		
		
		$sql .= " order by date desc";
		$sql .=  " limit " . $startFrom . ", " . $rowsPerPage;
			

		$this->mDb->query($sql);

		if ($orderId != null && $orderId != "")
				$this->mDb->bind(":id", $orderId);

		if ($dateFrom != null && $dateFrom != "")
				$this->mDb->bind(":date_from", $dateFrom);

		if ($dateTill != null && $dateTill != "")
			$this->mDb->bind(":date_till", $dateTill);

		if ($invoiceId != null && $invoiceId != "")
			$this->mDb->bind(":invoice", $invoice);

		if ($mealType != null && $mealType != "")
			$this->mDb->bind(":meal_type", $mealType);

		if ($memberId != null && $memberId != "")
			$this->mDb->bind(":member_id", $memberId);

		if ($status != null && $status != "")
		   $this->mDb->bind(":status", $status);

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
	 * Get count of all rows for a delivery date (YYYY-mm-dd)
	 * @param int $date
	 * @return int $retVal count of rows
	 */
	function getCountForDeliveryDate($date) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where delivery_date=:delivery_date";
	
		$this->mDb->query($sql);
		$this->mDb->bind(":delivery_date", $date);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}
	
	/**
	 * Get all rows for a deliver date ordered by school id
	 * @param int $date
 	 * @param int $row - starting row
	 * @param int rowsPerPage - rows to fetch

	 * @return array $rows 
	 */
	function getRowsForDeliveryDate($date,  $startFrom, $rowsPerPage) {

		$sql = "select " . $this->mTable . ".*, j_schools.ID as school_id from j_orders inner join j_student on j_orders.student_id = j_student.ID inner join j_schools on j_student.school_id = j_schools.ID  where delivery_date=:delivery_date order by school_id asc";
		$sql .=  " limit " . $startFrom . ", " . $rowsPerPage;

		$this->mDb->query($sql);
		$this->mDb->bind(":delivery_date", $date);
		$rows = $this->mDb->resultset();
		$this->mError = $this->mDb->getLastError();
		
		return $rows;

	}


	/**
	 * Get order by invoice number 
	 * @param string $invoice 
	 * @return array $row
	 */
	function getOrderByInvoice($invoice) {
		$retVal = 0;
		
		$sql ="SELECT * from " . $this->mTable . " where invoice=:invoice";
	
		$this->mDb->query($sql);
		$this->mDb->bind(":invoice", $invoice);

		$row = $this->mDb->single();
		
		$this->mError = $this->mDb->getLastError();
		return $row;

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
