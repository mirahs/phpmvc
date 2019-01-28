<?php
namespace core;


class Mysql
{
	protected $_dbh;
	protected $_rs;
	
	private $_sql;
	private $_insert_id;
	private $_query_times;
	private $_query_affected;
	
	private $_debug		= false;
	private $_charset	= 'utf8mb4'; //utf8mb4,gbk,latin1
	
	
	/**
	 * 创建Mysql实例
	 * 
	 * @param Array $config
	 * @return \core\Mysql
	 */
	public function __construct($config, $_debug = false)
	{
		$this->_debug = $_debug;
		$config['charset'] && $this->_charset = $config['charset'];
		try
		{
			$dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['database'] . ';charset=' . $this->_charset;
			$this->_dbh = new \PDO($dsn, $config['username'], $config['password']);
		}
		catch (\PDOException $e)
		{
			$this->outputError($e->getMessage());
		}
	}
	
	/**
	 * destruct 关闭数据库连接
	 */
	public function __destruct()
	{
		$this->_dbh = null;
	}
	
	/**
	 * 激活
	 */
	public function active()
	{
		$this->_dbh->exec("SET NAMES {$this->_charset}");
	}
	
	
	/**
	 * 插入一条或多条记录
	 *
	 * @param  String $table 表名 
	 * @param  array $bind  数据 Array
	 * @return Int insertId
	 */
	public function insert($table, $bind)
	{
		if (array_keys($bind) !== range(0, count($bind) - 1))
		{
			$cols 	= array_keys($bind);
			$sql 	= "INSERT INTO {$table} " . '(`' . implode('`,`', $cols) . '`) ' . 'VALUES (:' . implode(',:', $cols) . ');';
	
			$this->conn($sql, $bind);
		}
		else
		{
			$tmpArray 	= array();
			$cols 		= array_keys($bind[0]);
			foreach ($bind as $v)
			{
				$tmpArray[] = $this->format(':' . implode(',:', $cols), $v);
			}
			$sql = "INSERT INTO {$table} " . '(`' . implode('`, `', $cols) . '`) ' . 'VALUES (' . implode('),(', $tmpArray) . ');';
			$this->conn($sql);
		}
	
		$this->_insert_id 	  	= $this->_dbh->lastInsertId();
		$this->_query_affected	= $this->_rs->rowCount();
	
		return $this->_insert_id;
	}
	
	/**
	 * 替换(插入)一条或多条记录
	 *
	 * @param String $table   表名
	 * @param Array  $bind    数据  Array
	 * @return Int queryAffected
	 */
	public function replace($table, $bind)
	{
		if (array_keys($bind) !== range(0, count($bind) - 1))
		{
			$cols = array_keys($bind);
			$sql = "REPLACE INTO {$table} " . '(`' . implode('`,`', $cols) . '`) ' . 'VALUES (:' . implode(',:', $cols) . ') ;';
			$this->conn($sql, $bind);
		}
		else
		{
			$tmpArray = array();
			$cols 	  = array_keys($bind[0]);
			foreach ($bind as $v)
			{
				$tmpArray[] = $this->format(':' . implode(',:', $cols), $v);
			}
			$sql = "REPLACE INTO {$table} " . '(`' . implode('`,`', $cols) . '`) ' . 'VALUES (' . implode('),(', $tmpArray) . ') ;';
			$this->conn($sql);
		}
		
		$this->_query_affected = $this->_rs->rowCount();
		return $this->_query_affected;
	}
	
	/**
	 * 用新值更新原有表行中的各列
	 *
	 * @param String $table 表名
	 * @param array  $data  数据数组
	 * @param String $where 条件
	 * @param array  $bind  条件数组
	 * @return queryAffected
	 */
	public function update($table, $data, $where = null, $bind = null, $limit = 0)
	{
		$where && $where = $this->format($where, $bind);
		$set = array();
		foreach ($data as $col => $value)
		{
			$set[] = "`$col` = " . $this->quote($value);
		}
		$sql = "UPDATE {$table} " . 'SET ' . implode(',', $set) . (($where) ? " WHERE {$where}" : '') . ($limit ? ' LIMIT ' . ((int)$limit) : '');
		$this->conn($sql);
	
		$this->_query_affected = $this->_rs->rowCount();
	
		return $this->_query_affected;
	}
	
	/**
	 * 数据增加
	 *
	 * @param String $table  表名
	 * @param Array  $data   数据数组
	 * @param String $where  条件
	 * @param Array  $bind   条件数组
	 * @return queryAffected
	 */
	public function add($table, $data, $where = null, $bind = null)
	{
		$where && $where = $this->format($where, $bind);
		$set = array();
		foreach ($data as $col=>$val)
		{
			$set[] = "`$col` = `$col` + " . (float)$val;
		}
		$sql = "UPDATE {$table} " . 'SET ' . implode(', ', $set) . (($where) ? " WHERE {$where}" : '');
		$this->conn($sql, $bind);
	
		$this->_query_affected = $this->_rs->rowCount();
	
		return $this->_query_affected;
	}
	
	/**
	 * 数据减少
	 *
	 * @param String $table 表名
	 * @param Array $data   数据数组
	 * @param String $where 条件
	 * @param Array $bind   条件数组
	 * @return Int queryAffected
	 */
	public function cut($table, $data, $where = null, $bind = null)
	{
		$where && $where = $this->format($where, $bind);
		$set = array();
		foreach ($data as $col=>$val)
		{
			$set[] = "`$col` = `$col` - " . (float)$val;
		}
		$sql = "UPDATE {$table} " . 'SET ' . implode(', ', $set) . (($where) ? " WHERE {$where}" : '');
		$this->conn($sql, $bind);
	
		$this->_query_affected = $this->_rs->rowCount();
	
		return $this->_query_affected;
	}
	
	/**
	 * 删除记录
	 *
	 * @param String $table 表名
	 * @param String $where 条件
	 * @param Array  $bind  条件数组
	 * @return Int queryAffected
	 */
	public function delete($table, $where = null, $bind = null)
	{
		$sql = "DELETE FROM {$table} " . (($where) ? " WHERE $where" : '');
		$this->conn($sql, $bind);
	
		$this->_query_affected = $this->_rs->rowCount();
	
		return $this->_query_affected;
	}
	
	/**
	 * 得到數椐的行數
	 *
	 * @param String $sql
	 * @param Array  $bind 条件数组
	 * @return Int
	 */
	public function rows($sql = null, $bind = null)
	{
		$sql && $this->conn($sql, $bind);
		if ($this->_rs)
		{
			return $this->_rs->rowCount();
		}
		else
		{
			return 0;
		}
	}
	
	/**
	 * 得到一条数据数组
	 *
	 * @param String $sql
	 * @param Array  $bind 条件数组
	 * @return Array
	 */
	public function row($sql = null, $bind = null)
	{
		$rs = null;
		
		$sql && $this->conn($sql, $bind);
		
		if ($this->_rs)
		{
			$this->_rs->setFetchMode(\PDO::FETCH_ASSOC);
			$rs = $this->_rs->fetch();
		}
		
		return $rs;
	}
	
	/**
	 * 得到多条数据的数组
	 *
	 * @param String $sql
	 * @param Array $bind 条件数组
	 * @return Array
	 */
	public function dataArray($sql = null, $bind = null)
	{
		$rs	= null;
		
		$sql && $this->conn($sql, $bind);
		
		if ($this->_rs)
		{
			$this->_rs->setFetchMode(\PDO::FETCH_ASSOC);
			$rs = $this->_rs->fetchAll();
		}
		
		$this->free();
		
		return $rs;
	}
	
	/**
	 * 从结果集中取得一行(指定行)作为关联数组
	 *
	 * @param String $sql
	 * @param Array  $bind  条件数组
	 * @param String $keyField 可选 指定行
	 * @return Array
	 */
	public function fetchAssoc($sql = null, $bind = null, $keyField = null)
	{
		$rs		= array();
		
		$this->conn($sql, $bind);
		
		$datas	= $this->dataArray();
		
		if ($keyField)
		{
			foreach ($datas as $data)
			{
				$rs[ $data[$keyField] ]	= $data;
			}
		}
		else
		{
			foreach ($datas as $data)
			{
				$tmp					= array_values($data);
				$rs[ $tmp[0] ]			= $data;
			}
		}
	
		return $rs;
	}
	
	
	/**
	 * 发送一条 MySQL 查询
	 *
	 * @param String $sql SQL语句
	 * @param Array $bind
	 * @return resource
	 */
	public function conn($sql, $bind = null)
	{
		if ($sql)
		{
			$this->_sql = $this->format($sql, $bind);
		}
		if (true === $this->_debug) { $this->debug($this->_sql); }
		
		$this->_rs = $this->_dbh->query($this->_sql);
		$this->getPDOError();
		$this->_query_times++;
		
		return $this->_rs;
	}
	
	/**
	 * 清空内存
	 *
	 * @return boolean
	 */
	public function free()
	{
		if (is_resource($this->_rs))
		{
			$this->_rs->closeCursor();
		}
	
		return false;
	}
	
	
	
	/**
     * getPDOError 捕获PDO错误信息
     */
	private function getPDOError()
	{
		if ($this->_dbh->errorCode() != '00000')
		{
		  $arrayError = $this->_dbh->errorInfo();
		  $this->outputError($arrayError[2]);
		}
	}
	
	
	/**
     * debug
     * 
     * @param mixed $debuginfo
     */
	private function debug($debuginfo)
	{
		var_dump($debuginfo);
		echo "<br />";
	}
	
	
	/**
     * 输出错误信息
     * 
     * @param String $strErrMsg
     */
	private function outputError($strErrMsg)
	{
		throw new \Exception('MySQL Error: ' . $strErrMsg);
	}
	
	
	/**
	 * 返回最后一次插入的自增ID
	 *
	 * @return Int
	 */
	public function insertID()
	{
		return $this->_insert_id;
	}
	
	/**
	 * 返回查询的次数
	 *
	 * @return Int
	 */
	public function queryTimes()
	{
		return $this->_query_times;
	}
	
	/**
	 * 得到最后一次查询的sql
	 *
	 * @return String
	 */
	public function getSql()
	{
		return $this->_sql;
	}
	
	/**
	 * 得到最后一次更改的行数
	 *
	 * @return Int
	 */
	public function affected()
	{
		return $this->_query_affected;
	}
	
	
	
	/**
	 * 格式化sql语句
	 *
	 * @param String $sql
	 * @param Array $bind
	 * @return String
	 */
	private function format($sql, $bind = null)
	{
		if ($bind)
		{
			if (strpos($sql, '?') !== false)
			{
				return $this->quoteInto($sql, $bind);
			}
			else
			{
				return $this->bindValue($sql, $bind);
			}
		}
		else
		{
			return $sql;
		}
	}
	
	/**
	 * 格式化有数组的SQL语句
	 *
	 * @param  $sql
	 * @param  $bind
	 * @return string
	 */
	private function bindValue($sql, $bind)
	{
		$rs  = preg_split('/(\:[A-Za-z0-9_]+)\b/', $sql, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		$rs2 = array();
		foreach ($rs as $v)
		{
			if ($v[0] == ':')
			{
				$rs2[] = $this->quote($bind[substr($v, 1)]);
			}
			else
			{
				$rs2[] = $v;
			}
		}
		return implode('', $rs2);
	}
	
	/**
	 * 格式化问号(?)的SQL语句
	 *
	 * @param String $sql
	 * @param String $bind
	 * @return String
	 */
	private function quoteInto($text, $value)
	{
		return str_replace('?', $this->quote($value), $text);
	}
	
	/**
	 * 字符转义
	 *
	 * @param  String $value
	 * @return String
	 */
	private function quote($value)
	{
		if (is_array($value))
		{
			$vals = array();
			foreach ($value as $val)
			{
				$vals[] = $this->quote($val);
			}
			return implode(',', $vals);
		}
		else
		{
			return $this->_dbh->quote($value);
		}
	}
}
?>