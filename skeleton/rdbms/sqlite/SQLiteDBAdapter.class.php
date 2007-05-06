<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.DBAdapter');
  
  /**
   * Adapter for SQLite
   *
   * @see   http://sqlite.org/pragma.html
   * @see   xp://rdbms.DBAdapter
   * @see   xp://rdbms.mysql.MySQLConnection
   */
  class SQLiteDBAdapter extends DBAdapter {
    protected
      $map    = array();
      
    /**
     * Constructor
     *
     * @param   rdbms.DBConnection conn database connection
     */
    public function __construct($conn) {
      $this->map= array(
        'varchar'    => DB_ATTRTYPE_VARCHAR,
        'char'       => DB_ATTRTYPE_CHAR,
        'int'        => DB_ATTRTYPE_INT,
        'integer'    => DB_ATTRTYPE_INT,
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
     * Retrieve list of all databases
     *
     * @param   
     * @return  
     */
    public function getDatabases() {
      $dbs= array();
      $q= $this->conn->query('pragma database_list');
      while ($name= $q->next('file')) {
        $dbs[]= basename($name);
      }
      return $dbs;
    }
    
    /**
     * Retrive list of all tables
     *
     * @param   
     * @return  
     */
    public function getTables($database) {
      $t= array();
      $q= $this->conn->query('select tbl_name from sqlite_master where type= "table"');
      while ($table= $q->next('tbl_name')) {
        $t[]= $this->getTable($table);
      }
      
      return $t;
    }
    
    /**
     * Retrieve type from column description
     *
     * @param   string
     * @return  string
     */
    protected function typeOf($desc) {
      if (2 == sscanf($desc, '%[^(](%d)', $type, $length)) {
        return $type;
      }
      
      return $desc;
    }
    
    /**
     * Retrieve length from type
     *
     * @param   string desc
     * @return  int
     */
    protected function lengthOf($desc) {
      if (2 == sscanf($desc, '%[^(](%d)', $type, $length)) {
        return $length;
      }
      
      return 0;
    }
    
    /**
     * Get table information by name
     *
     * @param   string table
     * @return  rdbms.DBTable
     */
    public function getTable($table) {
      $t= new DBTable($table);

      $primaryKey= array();
      $q= $this->conn->query('pragma table_info(%s)', $table);
      
      while ($record= $q->next()) {
        $t->addAttribute(new DBTableAttribute(
          $record['name'],
          $this->map[$this->typeOf($record['type'])],
          $record['pk'],
          !$record['notnull'],
          $this->lengthOf($record['type']),
          0,
          0
        ));
        
        if ($record['pk']) $primaryKey[$record['name']]= TRUE;
      }
      
      $q= $this->conn->query('pragma index_list(%s)', $table);
      while ($indexInfo= $q->next()) {
        $qi= $this->conn->query('pragma index_info(%s)', $indexInfo['name']);
        
        $indexCols= $qi->next();
        
        // TBI: Are indices over multiple columns possible? Do they
        // return more than one row here?
        $dbindex= $t->addIndex(new DBIndex(
          $indexInfo['name'],
          array($indexCols['name'])
        ));
        
        $dbindex->unique= (bool)$indexInfo['unique'];
        $dbindex->primary= isset($primaryKey[$indexCols['name']]);
      }
      
      return $t;
    }
  }
?>
