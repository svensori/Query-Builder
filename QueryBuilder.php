<?php 
	class QueryProps{
		protected $connection;
		protected $select;
		protected $where;
		protected $join;
		protected $where_vals;
		protected $order;
		protected $num_rows;
		protected $encrypt_column;

		protected function clearQuery(){
			$this->select = null;
			$this->join = null;
			$this->where = null;
			$this->where_vals = null;
			$this->order = null;
			$this->num_rows = 0;
		}
	}

	class QueryBuilder extends QueryProps{
		public function __construct(PDO $connection){
			$this->connection = $connection;
		}

		public function select($tablename, Array $fields = null){
			$this->clearQuery();
			$this->select = "SELECT ". $this->selectParser($fields) . " FROM `{$tablename}` ";
		}

		public function join($table, $on, $type = null){
			$this->join .= "{$type} JOIN `{$table}` ON {$on} ";
		}

		public function where(Array $fields, $type = null){
			$this->where = "WHERE " . $this->whereParser($fields, $type);
		}

		public function order($column, $type = 'ASC'){
			$this->order = "ORDER BY {$column} {$type}";
		}

		private function selectParser(Array $fields = null){
			if (!empty($fields))
				return implode(', ', $fields);
			else
				return '*';
		}

		private function whereParser(Array $fields, $type = null){
			$operator = ($type == 'not') ? '!= ?' : '= ?';
			$where = '';

			if (!empty($fields)) {
				$this->where_vals = implode(", ", $fields);
				$count = 0;
				foreach ($fields as $column => $value) {
					$where .= "`{$column}` {$operator}";
					$count++;
					if($count != sizeof($fields))
						$where .= ' AND ';
				}
			}

			return $where;
		}

		private function prepareStatement(){
			return $this->connection->prepare($this->select . $this->join . $this->where . $this->order);
		}

		public function getRow(){
			$stmt = $this->prepareStatement();
			$stmt->execute([$this->where_vals]);
			$this->num_rows = $stmt->rowCount();

			if($this->encryptColumn($this->encrypt_column)){
				foreach ($stmt->fetch(PDO::FETCH_ASSOC) as $key => $value) {
					$data[$key] = ($this->encrypt_column == $key) ? Crypto::encrypt($value) : $value;
				}
				return $data;
			}else
				return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		public function getAll(){
			$stmt = $this->prepareStatement();
			$stmt->execute([$this->where_vals]);
			$this->num_rows = $stmt->rowCount();

			if($this->encryptColumn($this->encrypt_column)){
				$data = array();
				foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
					$sub_data = array();
					foreach ($row as $key => $value) {
						$sub_data[$key] = ($this->encrypt_column == $key) ? Crypto::encrypt($value) : $value;
					}
					array_push($data, $sub_data);
				}

				return $data;
			}else
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		public function numrows(){
			return $this->num_rows;
		}

		public function encryptColumn($column_name = null){
			if (isset($column_name)) {
				$this->encrypt_column = $column_name;
				return true;
			}else{
				return false;
			}
		}
	}
 ?>