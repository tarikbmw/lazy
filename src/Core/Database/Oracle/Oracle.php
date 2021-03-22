<?php 
namespace Core\Database\Oracle;
use Core\Database\Exception\Invalid;
use Core\Database\Exception\Connect;

/**
 * Object orient OCI extention
 * @author tarik
 *
 */
class Oracle
{
		/**
		 * Database instance
		 * @var unknown
		 */
		private $db;
		
		/**
		 * Current statement
		 * @var unknown
		 */
		private $statement;
		
		/**
		 * Current error
		 * @var string
		 */
		public 	$error;
		
		/**
		 * Current resultset
		 * @var unknown
		 */
		private $result;
	
		/**
		 * 
		 * @param string 	$host
		 * @param string 	$username
		 * @param string 	$password
		 * @param boolean 	$bPersistent	- use persisctent connection
		 */
		public function __construct($host, $username, $password, $bPersistent=true)
		{
			if (!$bPersistent)
			{				
				$this->db = oci_connect($username, $password, $host, 'AL32UTF8');
		
				if (!$this->db)
				{
					$err = oci_error();
					$this->error = $err['message'];
					throw new Connect($err['message']);					
				}

				oci_execute(oci_parse($this->db, 'alter session set NLS_NUMERIC_CHARACTERS=\'. \''));
				oci_execute(oci_parse($this->db, 'alter session set NLS_DATE_FORMAT=\'DD.MM.RR\''));
				
				
				return;
			}
						
			$this->db = oci_pconnect($username, $password, $host, 'AL32UTF8');
			if (!$this->db)
			{
				$err = oci_error();
				$this->error = $err['message'];
				throw new Connect($err['message']);
			}
			
			oci_execute(oci_parse($this->db, 'alter session set NLS_NUMERIC_CHARACTERS=\'. \''));
			oci_execute(oci_parse($this->db, 'alter session set NLS_DATE_FORMAT=\'DD.MM.RR\''));				
		}
		
		public function __destruct()
		{
			if ($this->db)
				oci_close();
		}
		
		
		/**
		 * 
		 * @param unknown $query
		 * @throws Invalid
		 * @return \Core\Database\Oracle\unknown
		 */
		public function prepare($query)
		{
			 $this->statement = oci_parse($this->db, $query);
			 if (!$this->statement)
			 {
			 	$error = oci_error();
			 	$this->error = $error['message'];
			 	throw new Invalid("Prepare statement failed, {$this->error}");
			 }
			 
			 return $this->statement;
		}
		
		
		/**
		 * 
		 * @param string $statement
		 * @throws Invalid
		 * @return \Core\Database\Oracle\unknown
		 */
		public function execute($statement=NULL)
		{
			if (!$statement)
				$statement = $this->statement;
						
			
			if (!$statement)
			{				
				$this->error = "Could not execute NULL statement.";				
				throw new Invalid("Could not execute NULL statement.");
			}
		
			
			$this->result = oci_execute($statement, OCI_COMMIT_ON_SUCCESS);
								
			
			if (!$this->result)
			{				
				$error = oci_error();
				$this->error = $error['message'];
				throw new Invalid("Statement execute failed, {$this->error}");
			}
			
			return $this->result;

		}
		
		
		/**
		 * 
		 * @param string $statement
		 * @throws Invalid
		 * @return multitype:multitype:
		 */
		public function fetch_array($statement=NULL)
		{
			if (!$statement)
				$statement = $this->statement;
			
			if (!$statement)
			{
				$this->error = "Could not fetch NULL statement.";
				throw new Invalid("Could not fetch NULL statement.");
			}

			$result = array();
		
			oci_fetch_all($statement, &$result);
			oci_free_statement($statement);
						
			if (!$result)
			{
				$error = oci_error();
				$this->error = $error['message'];
				throw new Invalid("Statement fetch failed, {$this->error}");
			}		

			return $result;
		}
		
		
		/**
		 * 
		 * @throws Invalid
		 * @return resource
		 */
		public function new_cursor()
		{
			$cursor = oci_new_cursor($this->db);
			if (!$cursor)
			{
				$error = oci_error($this->statement);
				$this->error = $error['message'];
				
				throw new Invalid("Cursor creation failed, {$this->error}");
			}
			
			return $cursor;
		}
		
				
		public function bind($name, &$value, $type=\SQLT_CHR, $max_length=-1, $statement=NULL)
		{
			if (!$statement)
				$statement = $this->statement;			
			
			 $result = oci_bind_by_name($statement, $name, $value, $max_length, $type);
			 
			 if (!$result)
			 	throw new Invalid("Binding `$name` failed.");
			 
			 return $result;
		}
		
		public function getStatement()
		{
			return $this->statement;
		}
		
		public function free_statement($statement)
		{
			if (!$statement)
				$statement = $this->statement;
			
			if (is_resource($statement))
				oci_free_statement($statement);
			
			$this->statement = NULL;
		}
}

?>