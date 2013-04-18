<?php
define( "MOD_DB_HOST", '127.0.0.1' );
define( "MOD_DB_NAME", 'mashup' );
define( "MOD_DB_USER", 'java' );
define( "MOD_DB_PASS", 'java' );

class MashupDatabaseConnector {
	private $connection = null;

	public $db_error = null;

	private static $db;


	function imports_db(){
		$this->init();
	}

	function __construct ( ) {
		$this->init();
	}

	function init ( ) {
		$this->connection = mysql_connect(MOD_DB_HOST, MOD_DB_USER, MOD_DB_PASS, true);
		if (!$this->connection ) {
			$this->db_error = 'Error creating connection: '.mysql_error();
			return;
		}
		if ( !mysql_select_db(MOD_DB_NAME, $this->connection ) ) {
			$this->db_error = 'Can\'t use '.MOD_DB_NAME.' '.mysql_error();
		}
	}

	function get_db_link () {
		return $this->connection;
	}

	function get_error() {
		return $this->db_error;
	}

	function execute_sql ( $sql, $returnObject=true ) {
		$db_error = null;
		$returnData = null;
		if ( $this->connection == null ) {
			$this->db_error = "Connection to database not initialized";
		}
		else {
			$result = mysql_query ( $sql, $this->connection );
			if ( !$result ) {
				$this->db_error = mysql_error();
				return null;
			}
			if ( $returnObject ) {
				$returnData = array();
				if ( mysql_num_rows($result) != 0 ) {
					while (($row = mysql_fetch_assoc($result))) {
						$rowData = new stdClass;
						foreach ($row as $k => $v ){
							$rowData->$k = $v;
						}
						$returnData[] = $rowData;
					}
				}
			}
			else {
				$returnData = $result;
			}
		}
		return $returnData;
	}

	public static function GetInstance () {
		if ( empty(self::$db) ) {
			self::$db = new MashupDatabaseConnector();
		}
		return self::$db;
	}
}
?>