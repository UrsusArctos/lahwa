<?php // (Abysmal Engine HLPR) MySQL wrapper
declare(encoding='UTF-8');
//
class MySQL
{	// Persistent (by 'static class variable' PHP magic) connection
	static $mysqli = null;
	// Regular variables
	private $rhandle = null;

	function __construct()
	{	// This is supposed to be invoked once per script lifetime
		if (is_null(MySQL::$mysqli)) { MySQL::$mysqli = mysqli_init(); };
		// Perform actual connection
		if (!mysqli_real_connect(MySQL::$mysqli,MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD,MYSQL_DBASE,MYSQL_PORT))
		{
			throw new \Exception('MySQL connection failed');
		};
		// Post-init
		MySQL::$mysqli->set_charset("utf8");
	}
	
	function __destruct()
	{
		// Clear MySQL
		self::Cleanup();
	}
	
	private function Cleanup()
	{
		if (($this->rhandle!==null)&&(!is_bool($this->rhandle)))
		{
			mysqli_free_result($this->rhandle);
			$this->rhandle = null;
		};
	}
	
	private function GenericQuery(string $querystr)
	{
		/*	Quickref: returns
				+ FALSE on failure
				+ TRUE if query was generally successful (did not cause a MySQL error)
				+ MYSQL RESULT object if the query was any of SELECT/SHOW/DESCRIBE/EXPLAIN
		*/
		self::Cleanup();		
		return (mysqli_query(MySQL::$mysqli,$querystr,MYSQLI_STORE_RESULT));
	}
	
	public function SelectQuery(string $querystr) 
	{
		/* 	Quickref: returns
				+ NULL on failure
				+ NULL if the result set is empty or not meant to be returned
				+ Associative Array if query was successful */
		$this->rhandle = self::GenericQuery($querystr);
		if (!is_bool($this->rhandle))
		{
			$rdata = mysqli_fetch_all($this->rhandle,MYSQLI_ASSOC);
			return ((count($rdata)>0) ? $rdata : null);
		} else return null;
	}
	
	public function InsertUpdateQuery(string $querystr)
	{
		/*	Quickref: returns
				+ last value of the AUTOINCREMENT field or zero if not applicable, both intvals
				+ FALSE on query failure */
		$this->rhandle = self::GenericQuery($querystr);
		if (!is_bool($this->rhandle)) return false;
		return ($this->rhandle ? intval(mysqli_insert_id(MySQL::$mysqli)) : false);
	}
	
	public function ErrorNo()
	{
		return mysqli_errno(MySQL::$mysqli);
	}

	public function ErrorText()
	{
		return self::ErrorNo().' "'.mysqli_error(MySQL::$mysqli).'"';
	}
	
	public function RealEscapeString($instr)
	{
		return mysqli_real_escape_string(MySQL::$mysqli,$instr);
	}
};

?>