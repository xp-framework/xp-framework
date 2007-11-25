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
        'bit'        => DB_ATTRTYPE_TINYINT,
        'date'       => DB_ATTRTYPE_DATE,
        'datetime'   => DB_ATTRTYPE_DATETIME,
        'timestamp'  => DB_ATTRTYPE_TIMESTAMP,
        'tinytext'   => DB_ATTRTYPE_TEXT,
        'mediumtext' => DB_ATTRTYPE_TEXT,
        'text'       => DB_ATTRTYPE_TEXT,
        'enum'       => DB_ATTRTYPE_ENUM,
        'decimal'    => DB_ATTRTYPE_DECIMAL,
        'float'      => DB_ATTRTYPE_FLOAT,
        'blob'       => DB_ATTRTYPE_TEXT,
        'mediumblob' => DB_ATTRTYPE_TEXT
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
      $q= $this->conn->query('show databases');
      while ($name= $q->next()) {
        $dbs[]= $name[key($name)];
      }
      return $dbs;
    }

    /**
     * Get tables by database
     *
     * @param   string database default NULL if omitted, uses current database
     * @return  rdbms.DBTable[] array of DBTable objects
     */
    public function getTables($database= NULL) {
      $t= array();
      $database= $this->database($database);
      $q= $this->conn->query(
        'show tables from %c',
        $database
      );
      while ($table= $q->next()) {
        $t[]= $this->getTable($table[key($table)], $database);
      }
      return $t;
    }

    /**
     * Get table by name
     *
     * @param   string table
     * @param   string database default NULL if omitted, uses current database
     * @return  rdbms.DBTable a DBTable object
     */
    public function getTable($table, $database= NULL) {
      $t= new DBTable($table);

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
      $q= $this->conn->query('describe %c', $this->qualifiedTablename($table, $database));
      while ($record= $q->next()) {
        preg_match('#^([a-z]+)(\(([0-9,]+)\))?#', $record['Type'], $regs);

        $t->addAttribute(new DBTableAttribute(
          $record['Field'],
          $this->map[$regs[1]],
          strstr($record['Extra'], 'auto_increment'),
          !(empty($record['Null']) || ('NO' == $record['Null'])),
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
      $q= $this->conn->query('show keys from %c', $this->qualifiedTablename($table, $database));
      $key= NULL;
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

      // Get foreign key constraints
      // in mysql the only way is to parse the creat statement
      $createTableString= $this->conn->query('show create table %c', $this->qualifiedTablename($table, $database))->next('Create Table');
      for ($i= 0; $i < strlen($createTableString); $i++) {
        switch ($createTableString{$i}) {
          case '`':
          $this->parseQuoteString($createTableString, $i);
          break;

          case '(':
          $tableConstraints= $this->filterConstraints($this->extractParams($this->parseBracerString($createTableString, $i)));
          foreach ($tableConstraints as $tableConstraint) {
            if (strstr($tableConstraint, 'FOREIGN KEY') === FALSE) continue;
            $t->addForeignKeyConstraint($this->parseForeignKeyString($tableConstraint));
          }
          break;
        }
      }
      return $t;
    }

    /**
     * Get full table name with database if possible
     *
     * @param   string table
     * @param   string database default NULL if omitted, uses current database
     * @return  string full table name
     */
    private function qualifiedTablename($table, $database= NULL) {
      $database= $this->database($database);
      if (NULL !== $database) return $database.'.'.$table;
      return $table;
    }

    /**
     * Get the current database
     *
     * @param   string database default NULL if omitted, uses current database
     * @return  string full table name
     */
    private function database($database= NULL) {
      if (NULL !== $database) return $database;
      return $this->conn->query('select database() as db')->next('db');
    }

    /**
     * get the foreign key object from a string
     *
     * @param   string parsestring
     * @return  rdbms.DBForeignKeyConstraint
     */
    private function parseForeignKeyString($string) {
      $constraint=   new DBForeignKeyConstraint();
      $quotstrings=  array();
      $bracestrings= array();
      $attributes=   array();
      $pos= 10;
      while (++$pos < strlen($string)) {
        switch ($string{$pos}) {
          case '`':
          $quotstrings[]= $this->parseQuoteString($string, $pos);
          break;

          case '(':
          $bracestrings[]= $this->parseBracerString($string, $pos);
          break;
        }
      }
      foreach ($bracestrings as $bracestring) {
        $params= $this->extractParams($bracestring);
        foreach ($params as $i => $param) $params[$i]= substr($param, 1, -1);
        $attributes[]= $params;
      }
      $constraint->setKeys(array_combine($attributes[0], $attributes[1]));
      $constraint->setName($quotstrings[0]);
      $constraint->setSource($quotstrings[1]);
      return $constraint;
    }

    /**
     * get the text inner a quotation
     *
     * @param   string parsestring
     * @param   &int position where the quoted string begins
     * @return  string inner quotation
     */
    private function parseQuoteString($string, &$pos) {
      $quotedString= '';
      while ($pos++ < strlen($string)) {
        switch ($string{$pos}) {
          case '`':
          return $quotedString;

          default:
          $quotedString.= $string{$pos};
        }
      }
      return $quotedString;
    }

    /**
     * get the text inner bracers
     *
     * @param   string parsestring
     * @param   &int position where the bracered string begins
     * @return  string inner bracers
     */
    private function parseBracerString($string, &$pos) {
      $braceredString= '';
      while ($pos++ < strlen($string)) {
        switch ($string{$pos}) {
          case ')':
          return $braceredString;
          break;

          case '(':
          $braceredString.= $string{$pos};
          $braceredString.= $this->parseBracerString($string, $pos).')';
          break;

          case '`':
          $braceredString.= $string{$pos};
          $braceredString.= $this->parseQuoteString($string, $pos).'`';
          break;

          default:
          $braceredString.= $string{$pos};
        }
      }
      return $braceredString;
    }

    /**
     * get the single params in a paramstring
     *
     * @param   string paramstring
     * @return  string[] paramstrings
     */
    private function extractParams($string) {
      $paramArray= array();
      $paramString= '';
      $pos= 0;
      while ($pos < strlen($string)) {
        switch ($string{$pos}) {
          case ',':
          $paramArray[]= trim($paramString);
          $paramString= '';
          break;

          case '(':
          $paramString.= $string{$pos};
          $paramString.= $this->parseBracerString($string, $pos).')';
          break;

          case '`':
          $paramString.= $string{$pos};
          $paramString.= $this->parseQuoteString($string, $pos).'`';
          break;

          default:
          $paramString.= $string{$pos};
        }
        $pos++;
      }
      $paramArray[]= trim($paramString);
      return $paramArray;
    }

    /**
     * filter the contraint parameters in a create table paramter string array
     *
     * @param   string[] array with parameter strings
     * @return  string[] constraint strings
     */
    private function filterConstraints($params) {
      $constraintArray= array();
      foreach ($params as $param) if ('CONSTRAINT' == substr($param, 0, 10)) $constraintArray[]= $param;
      return $constraintArray;
    }
  }
?>
