<?php
/* This class is part of the XP framework
 *
 * $Id: MySQLDBAdapter.class.php 9518 2007-02-28 13:08:45Z rene $
 */

  uses('rdbms.DBAdapter');
  
  /**
   * Adapter for MySQL
   *
   * @see   xp://rdbms.DBAdapter
   * @see   xp://rdbms.mysql.MySQLConnection
   */
  class PostgreSQLDBAdapter extends DBAdapter {

    /**
     * Constructor
     *
     * @param   rdbms.DBConnection conn
     */
    public function __construct($conn) {
      $this->map= array(
        'varchar'    => DB_ATTRTYPE_VARCHAR,
        'char'       => DB_ATTRTYPE_CHAR,
        'int'        => DB_ATTRTYPE_INT,
        'int4'       => DB_ATTRTYPE_INT,
        'bigint'     => DB_ATTRTYPE_NUMERIC,
        'int8'       => DB_ATTRTYPE_NUMERIC,
        'mediumint'  => DB_ATTRTYPE_SMALLINT,
        'smallint'   => DB_ATTRTYPE_SMALLINT,
        'int2'       => DB_ATTRTYPE_SMALLINT,
        'tinyint'    => DB_ATTRTYPE_TINYINT,
        'date'       => DB_ATTRTYPE_DATE,
        'time'       => DB_ATTRTYPE_DATETIME,
        'timestamp'  => DB_ATTRTYPE_TIMESTAMP,
        'mediumtext' => DB_ATTRTYPE_TEXT,
        'text'       => DB_ATTRTYPE_TEXT,
        'enum'       => DB_ATTRTYPE_ENUM,
        'decimal'    => DB_ATTRTYPE_DECIMAL,
        'float'      => DB_ATTRTYPE_FLOAT,
        'money'      => DB_ATTRTYPE_MONEY,
        'numeric'    => DB_ATTRTYPE_NUMERIC,
        'bool'       => DB_ATTRTYPE_BIT
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
      $q= $this->conn->query("Select db.datname as name from pg_database as db join pg_user as u on (db.datdba= u.usesysid) where u.usename=current_user;");
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
        "select * from information_schema.tables where table_schema='public' and table_catalog=%s", 
        $database
      );
      while ($table= $q->next('table_name')) {
        $t[]= $this->getTable($table, $database);
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
      $q= $this->conn->query(
        "Select
          column_name,
          udt_name,
          column_default,
          data_type,
          numeric_precision,
          numeric_scale,
          datetime_precision,
          character_maximum_length,
          is_nullable
        from
          information_schema.columns
        where
          table_schema='public'
          and table_catalog=%s
          and table_name=%s",
        $database,
        $table
      );
      while ($record= $q->next()) {
        $t->addAttribute(new DBTableAttribute(
          $record['column_name'], 
          $this->map[$record['udt_name']],
          (strpos($record['column_default'], 'nextval(') === 0),
          ($record['is_nullable'] != 'NO'),
          0, 
          $record['numeric_precision'], 
          $record['numeric_scale']
        ));
      }

      $q= $this->conn->query(
        "Select
          t.constraint_name as name,
          k.column_name as column
        from
          information_schema.table_constraints as t JOIN
          information_schema.key_column_usage as k on (k.constraint_name = t.constraint_name)
        where
          'PRIMARY KEY' = t.constraint_type
          and t.table_catalog = %s
          and t.table_name = %s",
        $database,
        $table
      );
      $key= NULL;
      while ($record= $q->next()) {
        if ($record['name'] != $key) {
          $index= $t->addIndex(new DBIndex($record['name'], array()));
          $key= $record['name'];
        }
        $index->unique= (TRUE);
        $index->primary= (TRUE);
        $index->keys[]= $record['column'];
      }

      $q= $this->conn->query(
        "Select
          t.constraint_name as name,
          k.column_name as column
        from
          information_schema.table_constraints as t JOIN
          information_schema.key_column_usage as k on (k.constraint_name = t.constraint_name)
        where
          'UNIQUE' = t.constraint_type
          and t.table_catalog = %s
          and t.table_name = %s",
        $database,
        $table
      );
      $key= NULL;
      while ($record= $q->next()) {
        if ($record['name'] != $key) {
          $index= $t->addIndex(new DBIndex($record['name'], array()));
          $key= $record['name'];
        }
        $index->unique= (TRUE);
        $index->primary= (FALSE);
        $index->keys[]= $record['column'];
      }

      $q= $this->conn->query(
        "Select
          t.constraint_name as name,
          t.table_catalog as db,
          t.table_name as tbl,
          k.column_name as col,
          r.unique_constraint_catalog as source_db,
          tt.table_name as source_tbl,
          tk.column_name as source_col
        from
          information_schema.table_constraints as t
          JOIN information_schema.referential_constraints as r on (t.constraint_name = r.constraint_name)
          JOIN information_schema.key_column_usage as k on (k.constraint_name = t.constraint_name)
          JOIN information_schema.table_constraints as tt on (r.unique_constraint_name = tt.constraint_name)
          JOIN information_schema.key_column_usage as tk on (
            r.unique_constraint_name = tk.constraint_name
            and k.ordinal_position = tk.ordinal_position
          )
        where
          t.constraint_type = 'FOREIGN KEY'
          and t.table_catalog = %s
          and t.table_name = %s",
        $database,
        $table
      );
      $key= NULL;
      while ($record= $q->next()) {
        if ($record['name'] != $key) {
          $constraint= new DBForeignKeyConstraint();
          $t->addForeignKeyConstraint($constraint);
          $key= $record['name'];
        }
        $constraint->addKey($record['col'], $record['source_col']);
        $constraint->setName($record['name']);
        $constraint->setSource($record['source_tbl']);
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
      return $this->conn->query('select current_database() as db')->next('db');
    }
  }
?>
