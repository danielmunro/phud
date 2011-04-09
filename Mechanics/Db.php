<?php

	/**
	 *
	 * Phud - a PHP implementation of the popular multi-user dungeon game paradigm.
     * Copyright (C) 2009 Dan Munro
	 * 
     * This program is free software; you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation; either version 2 of the License, or
     * (at your option) any later version.
	 * 
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
	 * 
     * You should have received a copy of the GNU General Public License along
     * with this program; if not, write to the Free Software Foundation, Inc.,
     * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
	 *
	 * Contact Dan Munro at dan@danmunro.com
	 * @author Dan Munro
	 * @package Phud
	 *
	 */
	namespace Mechanics;
	class Db extends \Mysqli
	{
		
		/**
		 * @access private
		 * @staticvar Db $instance Singleton instance of the Db
		 */
		private static $instance;
		
		/**
		 * @property MYSQLi_Result $result Result object for latest query
		 */
		private $result = null;
		
		/**
		 * @access private
		 * @method void __construct() __construct() Instantiates a connection to a MySQL database.
		 */
		protected function __construct()
		{
			parent::__construct('localhost', 'root', 'Gx9rm11r', 'mud');
		}
		
		/**
		 * @static Db getInstance() getInstance() Returns the Db instance
		 * @return Db Instance of the Db object
		 */
		public static function getInstance()
		{
		
			if(!(self::$instance instanceof self))
				self::$instance = new self();
			
			return self::$instance;
		}
		
		/**
		 * @method Db query() query(string $query_str, array $parameters) Performs a query with the given
		 * parameters, if any are supplied.
		 * @property string $query_str Query string to be run against the database
		 * @property array $parameters Parameters to be used in query
		 * @return Db Instance of the Db after performing the query
		 */
		public function query($query_str = '', $parameters = null, $debug = false)
		{
		
			if($query_str == '')
				throw new Exception('Query string is empty');
			
			$this->result = !empty($parameters) ? 
								parent::query($this->sanitize($query_str, $parameters)) :
								parent::query($query_str);
			if($debug)
				Debug::addDebugLine($this->sanitize($query_str, $parameters));
			
			return $this;
		}
		
		/**
		 * @method MYSQLi_Result getResult() getResult() Returns the result object created from the last query.
		 * @return MYSQLi_Result Result object from last query
		 */
		public function getResult()
		{
			
			return $this->result;
		}
		
		/**
		 * @method array fetch_objects() fetch_objects() This is an out-of-place function to get the
		 * an array of objects out of the  result object.
		 * @return array Results as objects
		 */
		public function fetch_objects()
		{

			$rows = array();
			
			if(empty($this->result))
				return $rows;
			
			while($row = $this->result->fetch_object())
				$rows[] = $row;
			
			return $rows;
		}	
		
		/**
		 * @access private
		 * @method string sanitize() sanitize(string $query_str, array $parameters) Prevents SQL injection through
		 * cleaning the parameters before querying.
		 * @property string $query_str Query string to be run against the database.
		 * @property array $parameters Parameters to be used in query.
		 * @return string A query string sanitized and ready to be run.
		 */
		private function sanitize($query_str, $parameters)
		{
		
			if(!is_array($parameters))
				$parameters = array($parameters);

			$param_count = substr_count($query_str, '?');
			$param_pos = strpos($query_str, '?');
			foreach($parameters as $parameter)
			{
				$param_length = strlen($parameter);
				$query_str = substr_replace($query_str, '"' . $this->real_escape_string($parameter) . '"', $param_pos, 1);
				$param_pos = strpos($query_str, '?', $param_pos + $param_length + 2);
			}
			return $query_str;		
		}
	}
?>
