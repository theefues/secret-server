<?php
class DatabaseModel
{
	protected	$_connection = null;
	public 	$_query = null;
	public	$_error = false;
	public	$_results = null;
	public 	$_count = 0;

	public function __construct()
	{
		try {
			$this->_connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE_NAME, DB_USERNAME, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_TIMEOUT => "5"));
			$this->_query = $this->_connection->prepare("SET NAMES utf8");
			$this->_query->execute();
		} catch (Exception  $e) {
			die("No MySQL Connection: " . $e->getMessage());
		}
		if (!$this->_connection) {
			die("No MySQL Connection!");
		}
	}

	/**
	 * Construct queries.
	 * @param string $sql Native SQL query. For prepared statements, you can use ? characters.
	 * @param array $params Substitution values in order.
	 * @return DatabaseModel|int Returns itself or the ID of the new entry if it was an insert query.
	 */
	public function query($sql, $params = array())
	{
		$this->deleteResults();

		$this->_error = false;

		if ($this->_query = $this->_connection->prepare($sql)) {
			$x = 1;
			if (count($params)) {
				foreach ($params as $param) {
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}

			if ($this->_query->execute()) {
				switch (strtolower(substr($sql, 0, 6))) {
					case 'insert':
					case 'replace':
						return $this->_connection->lastInsertId();
						break;

					default:
						$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
						$this->_count = $this->_query->rowCount();
						break;
				}
			} else {
				$this->_error = true;
			}
		}

		return $this;
	}

	/**
	 * Get everything from a given table.
	 * @param string $table Name of the table.
	 * @param array $where Criteria. Example: ['id', '=', 1]
	 * @return DatabaseModel
	 */
	public function get($table, $where)
	{
		return $this->action('SELECT *', $table, $where);
	}

	/**
	 * Delete a row from a given table.
	 * @param string $table Name of the table.
	 * @param array $where Criteria. Example: ['id', '=', 1]
	 * @return DatabaseModel
	 */
	public function delete($table, $where)
	{
		return $this->action('DELETE', $table, $where);
	}

	/**
	 * Execute query.
	 * @param string $action (SELECT, DELETE, UPDATE ...)
	 * @param string $table Name of table.
	 * @param array $where Criteria. Example: ['id', '=', 1]
	 * @return DatabaseModel
	 */
	public function action($action, $table, $where = array())
	{
		if (count($where) === 3) {
			$operators = array('=', '>', '<', '>=', '<=');

			$field      = $where[0];
			$operator   = $where[1];
			$value      = $where[2];

			if (in_array($operator, $operators)) {
				$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

				if (!$this->query($sql, array($value))->error()) {
					return $this;
				}
			}

			return false;
		}
	}

	/**
	 * Insert new row to a table.
	 * @param string $table Name of table.
	 * @param array $fields Values to insert. Example: ['id' => 1, 'name' => 'Josh']
	 * @return int ID of the new entry.
	 */
	public function insert($table, $fields = array(), $key = 'INSERT')
	{
		$keys   = array_keys($fields);
		$values = null;
		$x      = 1;

		foreach ($fields as $value) {
			$values .= "?";
			if ($x < count($fields)) {
				$values .= ', ';
			}
			$x++;
		}
		$sql = $key . " INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";
		return $this->query($sql, $fields);
	}

	/**
	 * Upsert.
	 * @param string $table Name of table.
	 * @param array $fields Values to upsert. Example: ['id' => 1, 'name' => 'Josh']
	 * @return int
	 */
	public function replace($table, $fields = array())
	{
		return $this->insert($table, $fields, 'REPLACE');
	}

	/**
	 * Update.
	 * @param string $table Name of table.
	 * @param int $id Identifier of row to update.
	 * @param array $fields Columns to update. Example: ['email' => 'foo@example.hu']
	 * @param string $id_field Default: 'id'. Name of the identifier.
	 * @return bool
	 */
	public function update($table, $id, $fields = array(), $id_field = 'id')
	{
		$set    = null;
		$x      = 1;
		foreach ($fields as $name => $value) {
			if (strtolower($value) == 'now()') {
				$set .= "`{$name}` = NOW()";
				unset($fields[$name]);
			} else {
				$set .= "`{$name}` = ?";
				if ($x < count($fields)) {
					$set .= ', ';
				}

				$x++;
			}
		}

		$sql = "UPDATE {$table} SET {$set} WHERE `{$id_field}` = {$id}";

		if (!$this->query($sql, $fields)->error()) {
			return true;
		}

		return false;
	}

	/**
	 * Get a column.
	 * @param string $table Name of table.
	 * @param int $id ID of row.
	 * @param string $data Name of column. Default: 'name'
	 * @param string $idtab Name of column to search. Default: 'id'
	 * @return string
	 */
	public function getDataStr($table, $id, $data = 'name', $idtab = 'id')
	{
		if (!isset($_cacheData[$table][$id][$data][$idtab])) {
			$d = $this->query('SELECT ' . $data . ' FROM ' . $table . ' WHERE ' . $idtab . ' = ?', [$id]);
			if ($d->count() == 1) {
				$d = $d->first()->$data;
			} else {
				$d = '';
			}
			$_cacheData[$table][$id][$data][$idtab] = $d;
		}
		return $_cacheData[$table][$id][$data][$idtab];
	}

	/**
	 * Get latest results.
	 * @return array
	 */
	public function results()
	{
		return (array) $this->_results;
	}

	/**
	 * The first element of the latest results.
	 * @return object
	 */
	public function first()
	{
		return isset($this->_results[0]) ? (array) $this->_results[0] : null;
	}

	/**
	 * Count of the latest results.
	 * @return int
	 */
	public function count()
	{
		return $this->_count;
	}

	/**
	 * Delete latest results.
	 */
	public function deleteResults()
	{
		$this->_results = NULL;
		$this->_query = NULL;
		$this->_error = NULL;
		$this->_count = NULL;
	}

	/**
	 * Check if there was an error.
	 * @return bool
	 */
	public function error()
	{
		return $this->_error;
	}

	/**
	 * Current time.
	 * @return string
	 */
	public function now()
	{
		return date("Y-m-d H:i:s");
	}
}
