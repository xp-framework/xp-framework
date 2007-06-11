<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.DBAdapter');
  
  /**
   * Adapter for sybase
   *
   * @see   xp://rdbms.DBAdapter
   * @see   xp://rdbms.sybase.SybaseConnection
   */
  class SybaseDBAdapter extends DBAdapter {
    public
      $map= array();
      
    /**
     * Constructor
     *
     * @param   Object conn database connection
     */
    public function __construct($conn) {
      $this->map= array(
        'binary'        => DB_ATTRTYPE_BINARY, 
        'bit'           => DB_ATTRTYPE_BIT,      
        'char'          => DB_ATTRTYPE_CHAR,     
        'datetime'      => DB_ATTRTYPE_DATETIME,   
        'datetimn'      => DB_ATTRTYPE_DATETIMN,   
        'decimal'       => DB_ATTRTYPE_DECIMAL,    
        'decimaln'      => DB_ATTRTYPE_DECIMALN,   
        'float'         => DB_ATTRTYPE_FLOAT,     
        'floatn'        => DB_ATTRTYPE_FLOATN, 
        'image'         => DB_ATTRTYPE_IMAGE,     
        'int'           => DB_ATTRTYPE_INT,      
        'intn'          => DB_ATTRTYPE_INTN,     
        'money'         => DB_ATTRTYPE_MONEY,     
        'moneyn'        => DB_ATTRTYPE_MONEYN, 
        'nchar'         => DB_ATTRTYPE_NCHAR,     
        'numeric'       => DB_ATTRTYPE_NUMERIC,    
        'numericn'      => DB_ATTRTYPE_NUMERICN,   
        'nvarchar'      => DB_ATTRTYPE_NVARCHAR,   
        'real'          => DB_ATTRTYPE_REAL,     
        'smalldatetime' => DB_ATTRTYPE_SMALLDATETIME,
        'smallint'      => DB_ATTRTYPE_SMALLINT,   
        'smallmoney'    => DB_ATTRTYPE_SMALLMONEY,
        'sysname'       => DB_ATTRTYPE_SYSNAME,    
        'text'          => DB_ATTRTYPE_TEXT,     
        'timestamp'     => DB_ATTRTYPE_TIMESTAMP,  
        'tinyint'       => DB_ATTRTYPE_TINYINT,    
        'varbinary'     => DB_ATTRTYPE_VARBINARY,  
        'varchar'       => DB_ATTRTYPE_VARCHAR 
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
        $q= $this->conn->query('select name from master..sysdatabases');
        while ($name= $q->next('name')) {
          $dbs[]= $name;
        }
      } catch (SQLException $e) {
        throw($e);
      }
      
      return $dbs;
    }

    /**
     * Creates temporary table needed for fetching table indexes
     *
     */
    protected function prepareTemporaryIndexesTable() {
      $this->conn->query('create table #indexes (
        keys varchar(200),
        name varchar(28),
        number int,
        status int
      )');
    }

    /**
     * Get indexes for a given table. Expects a temporary table to exist.
     *
     * @param   string table thee table's name
     * @return  rdbms.DBTable
     */    
    protected function dbTableObjectFor($table) {
      $t= new DBTable($table);
      
      // Get the table's attributes
      $q= $this->conn->query('
        select 
          c.name, 
          t.name as type, 
          c.status,
          c.length, 
          c.prec, 
          c.scale
        from 
          syscolumns c,
          systypes t 
        where 
          c.id= object_id(%s)
          and t.type = c.type 
          and t.usertype < 100 
          and t.name not in ("sysname", "nchar", "nvarchar")
      ', $table);
      while ($record= $q->next()) {
        // Known bits of status column:
        // 0x08 => NULLable
        // 0x80 => identity column
        $t->addAttribute(new DBTableAttribute(
          $record['name'], 
          $this->map[$record['type']],
          ($record['status'] & 0x80),
          ($record['status'] & 8), 
          $record['length'], 
          $record['prec'], 
          $record['scale']
        ));
      }
      delete($q);
        
      // This query is taken in part from sp_help (part of core sps from
      // SQL Server/11.0.3.3 ESD#6/P-FREE/Linux Intel/Linux 2.2.14 
      // i686/1/OPT/Fri Mar 17 15:45:30 CET 2000)
      $q= $this->conn->query('
        declare @i int
        declare @id int
        declare @last int
        declare @keys varchar(200)
        declare @key varchar(30)
        declare @obj varchar(30)

        delete from #indexes  

        select @obj= %s
        select @id= min(indid) from sysindexes where id= object_id(@obj)

        while @id is not NULL
        begin
          set nocount on
          select @keys= "", @i= 1
          while (@i <= 16) begin
          select @key= index_col(@obj, @id, @i) 
          if @key is NULL begin
            goto done
          end
          if @i > 1 begin
            select @keys= @keys + ","
          end 
          select @keys= @keys + @key
          select @i= @i + 1  
          end
          done:
          set nocount off


          insert #indexes select 
          @keys,
          i.name,
          v.number,
          i.status
          from 
          master.dbo.spt_values v, sysindexes i
          where 
          i.status & v.number = v.number
          and v.type = "I"
          and i.id = object_id(@obj)
          and i.indid = @id 

          select @last = @id
          select @id = min(indid) from sysindexes where id = object_id(@obj) and indid > @last
        end

        select * from #indexes', 
        $table
      );
      
      $keys= NULL;
      while ($record= $q->next()) {
        if ($keys != $record['keys']) {
          $index= $t->addIndex(new DBIndex(
            $record['name'],
            explode(',', $record['keys'])
          ));
          $keys= $record['keys'];
        }
        if (2 == $record['number']) $index->unique= TRUE;
        if ($record['status'] & 2048) $index->primary= TRUE;
      }
      
      return $t;
    }
    
    /**
     * Drops temporary created by prepareTemporaryIndexesTable()
     *
     */
    protected function dropTemporaryIndexesTable() {
      $this->conn->query('drop table #indexes');
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
        $this->prepareTemporaryIndexesTable();
        
        // Get all tables
        $q= $this->conn->query('
          select 
            o.name 
          from 
            %c..sysobjects o 
          where 
            o.type = "U"          -- User table
            and o.name not in (   -- Replication tables
              "rs_threads", 
              "rs_lastcommit", 
              "ticket_detail", 
              "ticket_hist", 
              "ticket_result"
            )
          ',
          $database
        );
        if ($q) while ($record= $q->next()) {
          $t[]= $this->dbTableObjectFor($record['name']);
        }
        
      } catch (SQLException $e) {
        delete($t);
      } finally(); {
        $this->dropTemporaryIndexesTable();
        if ($e) throw($e);
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
      try {
        $this->prepareTemporaryIndexesTable();
        $t= $this->dbTableObjectFor($table);
      } catch (SQLException $e) {
        delete($t);
      } finally(); {
        $this->dropTemporaryIndexesTable();
        if ($e) throw($e);
      }
      
      return $t;
    }
  }
?>
