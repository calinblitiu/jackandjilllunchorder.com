<?php
require_once("generic-table.php");
class SubscriptionItems extends GenericTable {

	var $mTable = "j_subscription_items";

	
	/**
	 * Get count of all rows for a member/product pair
	 * @param int $memberId - memberid
	 * @parm int $productId
	 * @return int $retVal count of rows
	 */
	function getCountForMemberAndProduct($memberId, $productId) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . "where member_id= :member_id and product_id=:product_id";
	
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
	 * Get count of all rows for a member
	 * @param int $memberId - memberid
	 * @param string $search
	 * @return int $retVal count of rows
	 */
	function getCountForMember($memberId, $search) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where member_id= :member_id";
		if ($search != null)	
			$sql .= " and product_id in (select ID from j_subscriptions where match(name) against ('" . $search . "'))";
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
	 * Get all rows for a member
	 * @param int $memberId - memberid
	 * @param string $search
 	 * @param int $row - starting row
	 * @param int rowsPerPage - rows to fetch
	 * @param string $sort
	 * @return array $rows 
	 */
	function getRowsForMember($memberId, $search, $startFrom, $rowsPerPage, $sort) {
		$arrSort = ["dateasc"=>"ID asc", "datedesc"=>"ID desc"];
		$sort = $arrSort[$sort];
		
		$sql ="SELECT * from " . $this->mTable . " where member_id = :member_id ";
		if ($search != null)	
			$sql .= " and product_id in (select ID from j_subscriptions where match(name) against ('" . $search . "'))";
			
		$sql .= " order by " . $sort ;
		$sql .=  " limit " . $startFrom . ", " . $rowsPerPage;


		$this->mDb->query($sql);
		$this->mDb->bind(":member_id", $memberId);
		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		
		return $rows;

	}
	

	/**
	 * Get count of all rows for a subscription
	 * @param int $subsId
	 * @param string $search
	 * @return int $retVal count of rows
	 */
	function getCountForSubscription($subsId, $search) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where purchase_id= :subscription_id";
		$this->mDb->query($sql);
		$this->mDb->bind(":subscription_id", $subsId);
		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Get all rows for an subscription
	 * @param int $subsId 
 	 * @param int $row - starting row
	 * @param int rowsPerPage - rows to fetch
	 * @return array $rows 
	 */
	function getRowsForSubscription($subsId, $startFrom, $rowsPerPage) {
		
		$sql ="SELECT " . $this->mTable . ".*, j_products.name as productname, j_products.image as image from " . $this->mTable . " inner join j_products on j_products.ID = " . 
			$this->mTable . ".product_id where purchase_id = :subscription_id ";
		$sql .=  " limit " . $startFrom . ", " . $rowsPerPage;


		$this->mDb->query($sql);
		$this->mDb->bind(":subscription_id", $subsId);
		$rows = $this->mDb->resultset();
		
			// special code to add mealdeal row into resultset as the inner joins wont work
		$sql = " select * from " . $this->mTable . " where  purchase_id = :subscription_id and product_id=" .
		  MEAL_DEAL_ITEM_DISPLAY_ID . " limit 0,1";
		$this->mDb->query($sql);
		$this->mDb->bind(":subscription_id", $subsId);

		$row = $this->mDb->single();
		if ($row) {
			$rows[] = $row;
		}
		
		$this->mError = $this->mDb->getLastError();
		
		return $rows;

	}

	/**
	 * Get all rows for a subscription grouped by meal type
	 * @param int $orderId 
 	 * @param int $row - starting row
	 * @param int rowsPerPage - rows to fetch
	 * @return array $rows 
	 */
	function getGroupedRowsForSubscription($subsId, $startFrom, $rowsPerPage) {
		
		$sql ="SELECT " . $this->mTable . ".*, j_products.name as productname, j_products.image as image from " . $this->mTable . " inner join j_products on j_products.ID = " . 
			$this->mTable . ".product_id where purchase_id = :subscription_id ";
		$sql .=  " order by meal_type limit " . $startFrom . ", " . $rowsPerPage;


		$this->mDb->query($sql);
		$this->mDb->bind(":subscription_id", $subsId);
		$rows = $this->mDb->resultset();
		
			// special code to add mealdeal row into resultset as the inner joins wont work
		$sql = " select * from " . $this->mTable . " where  purchase_id = :subscription_id and product_id=" .
		  MEAL_DEAL_ITEM_DISPLAY_ID . " limit 0,1";
		$this->mDb->query($sql);
		$this->mDb->bind(":subscription_id", $subsId);

		$row = $this->mDb->single();
		if ($row) {
			$rows[] = $row;
		}
		
		$this->mError = $this->mDb->getLastError();
		
		return $rows;

	}
	

	/**
	 * Get count of all rows for a product
	 * @param int $productid
	 * @return int $retVal count of rows
	 */
	function getCountForProduct($productId) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where product_id= :product_id";
	
		$this->mDb->query($sql);
		$this->mDb->bind(":product_id", $productId);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


}

?>
