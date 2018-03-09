<?php
require_once($g_docRoot . "classes/generic-table.php");
class Products extends GenericTable {

	var $mTable = "j_products";


	/**
	 * Get count of all rows 
	 * @param string $name
	 * @param int $ftype
	 * @param int $recess
	 * @param int $lunch
	 * @param int $mealDeal
	 * @param int global
	 * @return int $retVal count of rows
	 */
	function getCount($name, $ftype, $recess, $lunch, $mealDeal, $global) {
		$retVal = 0;
		
		$sql ="SELECT count(*) as total from " . $this->mTable . " where ID > 0 ";
		if ($name != null && $name != "")
			$sql .= " and name like :name";
	 	if ($ftype != null && $ftype != "")
			$sql .= " and food_type=:food_type";
 		if ($recess != null && $recess != "")
			$sql .= " and flag_recess=:flag_recess";
 		if ($lunch != null && $lunch != "")
			$sql .= " and flag_lunch=:flag_lunch";
	 	if ($mealDeal != null && $mealDeal != "")
			$sql .= " and flag_meal_deal=:flag_meal_deal";
 		if ($global != null && $global != "")
			$sql .= " and flag_global=:flag_global";
		$this->mDb->query($sql);

		if ($name != null && $name != "")
	 		$this->mDb->bind(":name", "%" . $name . "%");
		if ($ftype != null && $ftype != "")
			$this->mDb->bind(":food_type", $ftype);
 		if ($recess != null && $recess != "")
			$this->mDb->bind(":flag_recess", $recess);
 		if ($lunch != null && $lunch != "")
			$this->mDb->bind(":flag_lunch", $lunch);
	 	if ($mealDeal != null && $mealDeal != "")
			$this->mDb->bind(":flag_meal_deal", $mealDeal);
 		if ($global != null && $global != "")
			$this->mDb->bind(":flag_global", $global);

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
	 * @param int $ftype
	 * @param int $recess
	 * @param int $lunch
	 * @param int $mealDeal
	 * @param int global
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getList($name, $ftype, $recess, $lunch, $mealDeal, $global, $startFrom, $rowsPerPage, $sort) {
		$arrSort = ["date_desc"=>"date_added desc", "date_asc"=>"date_added asc", 
			    "name_desc"=>"name desc", "name_asc"=>"name asc",
				"price_asc"=>"price asc", "price_desc"=>"price desc"];
		
		$sql ="SELECT * from " . $this->mTable . " where ID > 0";
		if ($name != null && $name != "")
			$sql .= " and name like :name";
	 	if ($ftype != null && $ftype != "")
			$sql .= " and food_type=:food_type";
 		if ($recess != null && $recess != "")
			$sql .= " and flag_recess=:flag_recess";
 		if ($lunch != null && $lunch != "")
			$sql .= " and flag_lunch=:flag_lunch";
	 	if ($mealDeal != null && $mealDeal != "")
			$sql .= " and flag_meal_deal=:flag_meal_deal";
 		if ($global != null && $global != "")
			$sql .= " and flag_global=:flag_global";
		if ($sort == null || $sort == "")
			$sql .= " order by date_added desc ";
		else
			$sql .= " order by " . $arrSort[$sort];

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);

		if ($name != null && $name != "")
	 		$this->mDb->bind(":name", "%" . $name . "%");
		if ($ftype != null && $ftype != "")
			$this->mDb->bind(":food_type", $ftype);
 		if ($recess != null && $recess != "")
			$this->mDb->bind(":flag_recess", $recess);
 		if ($lunch != null && $lunch != "")
			$this->mDb->bind(":flag_lunch", $lunch);
	 	if ($mealDeal != null && $mealDeal != "")
			$this->mDb->bind(":flag_meal_deal", $mealDeal);
 		if ($global != null && $global != "")
			$this->mDb->bind(":flag_global", $global);


		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}


	/**
	 * Get  list of item of a food type
	 * @param int $ftype
	 * @param int $ignoreId
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getListOfType($ftype, $ignoreId, $startFrom, $rowsPerPage, $sort) {
		$arrSort = ["date_desc"=>"date_added desc", "date_asc"=>"date_added asc", 
			    "name_desc"=>"name desc", "name_asc"=>"name asc",
				"price_asc"=>"price asc", "price_desc"=>"price desc"];
		
		$sql ="SELECT * from " . $this->mTable . " where ID <> :ignore";
		$sql .= " and food_type=:food_type";
		
		$sql .= " order by " . $arrSort[$sort];

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);

		$this->mDb->bind(":food_type", $ftype);
		$this->mDb->bind(":ignore", $ignoreId);

		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}


	/**
	 * Get  list of items in a range of ids
	 * @param string ids
	 * @param int $row - starting row
	 * @param int $rowsPerPage - rows to fetch
	 * @param string $sort sort expression
	 * @return array $rows row of data
	 */
	function getListInRange($ids, $startFrom, $rowsPerPage, $sort) {
		$arrSort = ["date_desc"=>"date_added desc", "date_asc"=>"date_added asc", 
			    "name_desc"=>"name desc", "name_asc"=>"name asc",
				"price_asc"=>"price asc", "price_desc"=>"price desc"];
		
		$sql ="SELECT * from " . $this->mTable . " where ID  in (" . $ids . ")";
		$sql .= " order by " . $arrSort[$sort];

		$sql .= " limit " . $startFrom . "," . $rowsPerPage;
		$this->mDb->query($sql);
		$rows = $this->mDb->resultset();
		
		$this->mError = $this->mDb->getLastError();
		

		return $rows;

	}


	/**
	 * Get row by name 
	 * @return array $row
	 */
	function getRowByName($name) {
		$retVal = 0;
		
		$sql ="SELECT * from " . $this->mTable . " where name=:name";
		$this->mDb->query($sql);
		$this->mDb->bind(":name", $name);

		$row = $this->mDb->single();

		
		$this->mError = $this->mDb->getLastError();
		return $row;

	}


}
?>
