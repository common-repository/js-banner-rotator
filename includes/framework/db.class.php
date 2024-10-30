<?php
	class UniteDBBanner {
		
		private $wpdb;
		private $lastRowID;
		
		//Constructor - set database object
		public function __construct() {
			global $wpdb;
			$this->wpdb = $wpdb;
		}
		
		//Throw error
		private function throwError($message, $code=-1) {
			UniteFunctionsBanner::throwError($message,$code);
		}
		
		//Validate for errors
		private function checkForErrors($prefix="") {	
			if(function_exists('mysql_error')) {		
				if(mysql_error()) {
					$query = $this->wpdb->last_query;
					$message = $this->wpdb->last_error;
					
					if($prefix) $message = $prefix.' - <b>'.$message.'</b>';
					if($query) $message .=  '<br>---<br> Query: ' . $query;
					
					$this->throwError($message);
				}
			}
		}		
		
		//Insert variables to some table
		public function insert($table,$arrItems) {			
			$this->wpdb->insert($table, $arrItems);
			$this->checkForErrors("Insert query error");
			
			$this->lastRowID = $this->wpdb->insert_id;
						
			return($this->lastRowID);
		}
		
		//Get last insert id
		public function getLastInsertID() {
			$this->lastRowID = $this->wpdb->insert_id;
			return($this->lastRowID);			
		}		
		
		//Delete rows
		public function delete($table,$where) {			
			UniteFunctionsBanner::validateNotEmpty($table,"table name");
			UniteFunctionsBanner::validateNotEmpty($where,"where");
			
			$query = "delete from $table where $where";
			
			$this->wpdb->query($query);
			
			$this->checkForErrors("Delete query error");
		}		
		
		//Run some sql query
		public function runSql($query) {			
			$this->wpdb->query($query);			
			$this->checkForErrors("Regular query error");
		}
		
		
		//Insert variables to some table
		public function update($table, $arrItems, $where) {			
			$response = $this->wpdb->update($table, $arrItems, $where);
			
			if($response===false)
				UniteFunctionsBanner::throwError("no update action taken!");
				
			$this->checkForErrors("Update query error");
			
			return($this->wpdb->num_rows);
		}		
		
		//Get data array from the database
		public function fetch($tableName, $where="", $orderField="", $groupByField="", $sqlAddon="") {		
			$query = "select * from $tableName";
			if($where) $query .= " where $where";
			if($orderField) $query .= " order by $orderField";
			if($groupByField) $query .= " group by $groupByField";
			if($sqlAddon) $query .= " ".$sqlAddon;
			
			$response = $this->wpdb->get_results($query,ARRAY_A);
			
			$this->checkForErrors("fetch");
			
			return($response);
		}
		
		//Fetch only one item. if not found - throw error
		public function fetchSingle($tableName, $where="", $orderField="", $groupByField="", $sqlAddon="") {
			$response = $this->fetch($tableName, $where, $orderField, $groupByField, $sqlAddon);
			
			if(empty($response))
				$this->throwError("Record not found");
				
			$record = $response[0];
			
			return($record);
		}
		
		//Escape data to avoid sql errors and injections.
		public function escape($string) {
			return($this->wpdb->escape($string));
		}
		
	}	
?>