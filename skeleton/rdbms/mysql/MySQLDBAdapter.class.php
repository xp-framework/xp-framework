<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.DBAdapter');
  
  /**
   * Adapter for MySQL
   *
   * @see   xp://rdbms.DBAdapter
   * @see   xp://rdbms.mysql.MySQLConnection
   */
  class MySQLDBAdapter extends DBAdapter {

    /**
     * Constructor
     *
     * @param   Object conn database connection
     */
    public function __construct($conn) {
      $this->map= array(
        'varchar'    => DB_ATTRTYPE_VARCHAR,
        'char'       => DB_ATTRTYPE_CHAR,
        'int'        => DB_ATTRTYPE_INT,
        'bigint'     => DB_ATTRTYPE_NUMERIC,
        'mediumint'  => DB_ATTRTYPE_SMALLINT,
        'smallint'   => DB_ATTRTYPE_SMALLINT,
        'tinyint'    => DB_ATTRTYPE_TINYINT,
        'date'       => DB_ATTRTYPE_DATE,
        'datetime'   => DB_ATTRTYPE_DATETIME,
        'timestamp'  => DB_ATTRTYPE_TIMESTAMP,
        'mediumtext' => DB_ATTRTYPE_TEXT,
        'text'       => DB_ATTRTYPE_TEXT,
        'enum'       => DB_ATTRTYPE_ENUM,
        'decimal'    => DB_ATTRTYPE_DECIMAL,
        'float'      => DB_ATTRTYPE_FLOAT
      );
      parent::__construct($conn);
    }
    
    /**
     * Get databases
     *
     * @return  string[] databases
     */
    public function getDatabases() {
      $dbs= array();
      try {
        $q= $this->conn->query('show databases');
        while ($name= $q->next('name')) {
          $dbs[]= $name;
        }
      } catch (SQLException $e) {
        throw($e);
      }
      
      return $dbs;
    }
    
    /**
     * Get tables by database
     *
     * @param   string database
     * @return  rdbms.DBTable[] array of DBTable objects
     */
    public function getTables($database) {
      $t= array();
      try {
        $q= $this->conn->query('show tables');
        while ($table= $q->next()) {
          $t[]= $this->getTable($table[key($table)]);
        }
      } catch (SQLException $e) {
        throw($e);
      }
      
      return $t;
    }
    
    /**
     * Get table by name
     *
     * @param   string table
     * @return  rdbms.DBTable a DBTable object
     */
    public function getTable($table) {
      $t= new DBTable($table);
      try {
      
        // Get the table's attributes
        // +-------------+--------------+------+-----+---------------------+----------------+
        // | Field       | Type         | Null | Key | Default             | Extra          |
        // +-------------+--------------+------+-----+---------------------+----------------+
        // | contract_id | int(8)       |      | PRI | NULL                | auto_increment |
        // | user_id     | int(8)       |      |     | 0                   |                |
        // | mandant_id  | int(4)       |      |     | 0                   |                |
        // | description | varchar(255) |      |     |                     |                |
        // | comment     | varchar(255) |      |     |                     |                |
        // | bz_id       | int(6)       |      |     | 0                   |                |
        // | lastchange  | datetime     |      |     | 0000-00-00 00:00:00 |                |
        // | changedby   | varchar(16)  |      |     |                     |                |
        // +-------------+--------------+------+-----+---------------------+----------------+
        // 8 rows in set (0.00 sec)
        $q= $this->conn->query('describe %c', $table);
        while ($record= $q->next()) {
          preg_match('#^([a-z]+)(\(([0-9,]+)\))?#', $record['Type'], $regs);

          $t->addAttribute(new DBTableAttribute(
            $record['Field'], 
            $this->map[$regs[1]],
            strstr($record['Extra'], 'auto_increment'),
            !empty($record['Null']),
            $regs[3], 
            0, 
            0
          ));
        }
        
        // Get keys
        // +----------+------------+---------------+--------------+-------------+-----------+-------------+----------+--------+---------+
        // | Table    | Non_unique | Key_name      | Seq_in_index | Column_name | Collation | Cardinality | Sub_part | Packed | Comment |
        // +----------+------------+---------------+--------------+-------------+-----------+-------------+----------+--------+---------+
        // | contract |          0 | PRIMARY       |            1 | contract_id | A         |           6 |     NULL | NULL   |         |
        // | contract |          0 | contract_id_2 |            1 | contract_id | A         |           6 |     NULL | NULL   |         |
        // | contract |          1 | contract_id   |            1 | contract_id | A         |           6 |     NULL | NULL   |         |
        // | contract |          1 | contract_id   |            2 | user_id     | A         |           6 |     NULL | NULL   |         |
        // +----------+------------+---------------+--------------+-------------+-----------+-------------+----------+--------+---------+
        $q= $this->conn->query('show keys from %c', $table);
        while ($record= $q->next()) {
          if ($record['Key_name'] != $key) {
            $index= $t->addIndex(new DBIndex(
              $record['Key_name'],
              array()
            ));
            $key= $record['Key_name'];
          }
          $index->unique= ('0' == $record['Non_unique']);
          $index->primary= ('PRIMARY' == $record['Key_name']);
          $index->keys[]= $record['Column_name'];
        }
      } catch (SQLException $e) {
        throw($e);
      }
      
      return $t;
    }
  }
?>
