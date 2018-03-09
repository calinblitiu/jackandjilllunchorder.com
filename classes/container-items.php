<?php
require_once($g_docRoot . "classes/generic-table.php");

class ContainerItems extends GenericTable {

	var $mTable = "j_container_items";


	/**
	 * Get count of all rows for a container
	 * @param int contId
	 * @return int $retVal count of rows
	 */
	function getCountForAContainer($contId) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where container_id=:container_id ";
		$this->mDb->query($sql);

 		$this->mDb->bind(":container_id", $contId);

		$rows = $this->mDb->single();
		
		if (is_array($rows)) {
			$retVal = $rows["total"];
		}
		
		$this->mError = $this->mDb->getLastError();
		return $retVal;

	}


	/**
	 * Get  list for a container
	 * @param int $contId
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getListForAContainer($contId, $startFrom, $rowsPerPage) {
		
		$sql ="SELECT " . $this->mTable . ".*, j_products.name as productname from " . $this->mTable . " inner join j_products on " . $this->mTable .".product_id = j_products.ID where container_id=:container_id";
		$sql .= " order by ID";

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);

 		$this->mDb->bind(":container_id", $contId);
		$rows = $this->mDb->resultset();
		
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

	/**
	 * Get count of all rows for a container and a product id
	 * @param int $contId
	 * @param int $productId
	 * @return array $row
	 */
	function getRowForAContainerAndProduct($contId, $productId) {
		$retVal = 0;
		
		$sql ="SELECT * from " . $this->mTable . " where container_id=:container_id and product_id=:product_id";
		$this->mDb->query($sql);

 		$this->mDb->bind(":container_id", $contId);
		$this->mDb->bind(":product_id", $productId);

		$row = $this->mDb->single();
		
		
		$this->mError = $this->mDb->getLastError();
		return $row;

	}


	/**
	 * Get a container which contains all products specified
	 * @param array $arrProducts array of product ids
	 * @return array $row 
	 */
	function getContainerForACombination($arrProducts) {
		$retVal = 0;
		
		$sql ="SELECT * from " . $this->mTable . " where ";
		$and = "";
		foreach($arrProducts as $arr) {
			$sql .= $and . " product_id=" . $arr;
			$and = " or ";
		}
		$sql .= " order by container_id, qty desc ";
		$this->mDb->query($sql);

		$rows = $this->mDb->resultset();

		if (count($rows) == 0)
			$row = null;
		else if (count($rows) == 1)
		   $row = $rows[0];
	    else {

			    $rowId = "0"; 
				$selContainerId = $rows[0]["container_id"];
				$rowId = $rows[0]["ID"];
				$key = array_search($rows[0]["product_id"], $arrProducts);
				unset($arrProducts[$key]);
				foreach($rows as $row) {
					if (in_array($row["product_id"], $arrProducts) && 
						$row["container_id"] != $selContainerId) {
						$selContainerId = $row["container_id"];
						$rowId = $row["ID"];
					}
				
				}
				// get the container row
				$this->mDb->query("select * from " . $this->mTable . " where ID=" . $rowId);
				$row = $this->mDb->single();

		}

		$this->mError = $this->mDb->getLastError();
		return $row;

	}



}
?>
