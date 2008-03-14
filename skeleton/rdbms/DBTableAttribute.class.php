<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  define('DB_ATTRTYPE_BINARY',         0x0000);             
  define('DB_ATTRTYPE_BIT',            0x0001);               
  define('DB_ATTRTYPE_CHAR',           0x0002);              
  define('DB_ATTRTYPE_DATETIME',       0x0003);            
  define('DB_ATTRTYPE_DATETIMN',       0x0004);            
  define('DB_ATTRTYPE_DECIMAL',        0x0005);             
  define('DB_ATTRTYPE_DECIMALN',       0x0006);            
  define('DB_ATTRTYPE_FLOAT',          0x0007);             
  define('DB_ATTRTYPE_FLOATN',         0x0008);            
  define('DB_ATTRTYPE_IMAGE',          0x0009);             
  define('DB_ATTRTYPE_INT',            0x000A);               
  define('DB_ATTRTYPE_INTN',           0x000B);              
  define('DB_ATTRTYPE_MONEY',          0x000C);             
  define('DB_ATTRTYPE_MONEYN',         0x000D);            
  define('DB_ATTRTYPE_NCHAR',          0x000E);             
  define('DB_ATTRTYPE_NUMERIC',        0x000F);             
  define('DB_ATTRTYPE_NUMERICN',       0x0010);            
  define('DB_ATTRTYPE_NVARCHAR',       0x0011);            
  define('DB_ATTRTYPE_REAL',           0x0012);              
  define('DB_ATTRTYPE_SMALLDATETIME',  0x0013);         
  define('DB_ATTRTYPE_SMALLINT',       0x0014);         
  define('DB_ATTRTYPE_SMALLMONEY',     0x0015);       
  define('DB_ATTRTYPE_SYSNAME',        0x0016);          
  define('DB_ATTRTYPE_TEXT',           0x0017);           
  define('DB_ATTRTYPE_TIMESTAMP',      0x0018);        
  define('DB_ATTRTYPE_TINYINT',        0x0019);          
  define('DB_ATTRTYPE_VARBINARY',      0x001A);        
  define('DB_ATTRTYPE_VARCHAR',        0x001B);          
  define('DB_ATTRTYPE_ENUM',           0x001C);          
  define('DB_ATTRTYPE_DATE',           0x001D);
  
  /**
   * Represents a table's attribute
   *
   * @see   xp://rdbms.DBTable
   */
  class DBTableAttribute extends Object {
    public 
      $name=        '',
      $type=        -1,
      $ident=       FALSE,
      $nullable=    FALSE,
      $length=      0,
      $precision=   0,
      $scale=       0;
      
    /**
     * Constructor
     *
     * @param   string name
     * @param   int type
     * @param   bool identity default FALSE
     * @param   bool nullable default FALSE
     * @param   int length default 0
     * @param   int precision default 0,
     * @param   int scale default 0
     */
    public function __construct(
      $name, 
      $type, 
      $identity= FALSE, 
      $nullable= FALSE, 
      $length= 0, 
      $precision= 0, 
      $scale= 0
    ) {
      $this->name= $name;
      $this->type= $type;
      $this->identity= $identity;
      $this->nullable= $nullable;
      $this->length= $length;
      $this->precision= $precision;
      $this->scale= $scale;
      
    }
    
    /**
     * Returns whether this attribute is an identity field
     *
     * @return  bool
     */
    public function isIdentity() {
      return $this->identity;
    } 
    
    /**
     * Returns whether this attribute is nullable
     *
     * @return  bool
     */
    public function isNullable() {
      return $this->nullable;
    }
    
    /**
     * Returns this attribute's name
     *
     * @return  bool
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Returns this attribute's length
     *
     * @return  int length
     */
    public function getLength() {
      return $this->length;
    }

    /**
     * Returns a textual representation of the type, e.g. 
     * DB_ATTRTYPE_VARCHAR
     *
     * @return  string type
     */
    public function getTypeString() {
      static $map= array(
        'DB_ATTRTYPE_BINARY',   
        'DB_ATTRTYPE_BIT',     
        'DB_ATTRTYPE_CHAR',    
        'DB_ATTRTYPE_DATETIME',  
        'DB_ATTRTYPE_DATETIMN',  
        'DB_ATTRTYPE_DECIMAL',   
        'DB_ATTRTYPE_DECIMALN',  
        'DB_ATTRTYPE_FLOAT',   
        'DB_ATTRTYPE_FLOATN',   
        'DB_ATTRTYPE_IMAGE',   
        'DB_ATTRTYPE_INT',     
        'DB_ATTRTYPE_INTN',    
        'DB_ATTRTYPE_MONEY',   
        'DB_ATTRTYPE_MONEYN',   
        'DB_ATTRTYPE_NCHAR',   
        'DB_ATTRTYPE_NUMERIC',   
        'DB_ATTRTYPE_NUMERICN',  
        'DB_ATTRTYPE_NVARCHAR',  
        'DB_ATTRTYPE_REAL',    
        'DB_ATTRTYPE_SMALLDATETIME',
        'DB_ATTRTYPE_SMALLINT',  
        'DB_ATTRTYPE_SMALLMONEY',
        'DB_ATTRTYPE_SYSNAME',   
        'DB_ATTRTYPE_TEXT',    
        'DB_ATTRTYPE_TIMESTAMP', 
        'DB_ATTRTYPE_TINYINT',   
        'DB_ATTRTYPE_VARBINARY', 
        'DB_ATTRTYPE_VARCHAR',
        'DB_ATTRTYPE_ENUM',
        'DB_ATTRTYPE_DATE'
      );
      return $map[$this->type];
    }
    
    /**
     * Return this attribute's type name (for XP)
     *
     * @return  string type or FALSE if unknown
     */
    public function typeName() {
      switch ($this->type) {   
        case DB_ATTRTYPE_BIT:
          return 'bool';
          
        case DB_ATTRTYPE_DATETIME:
        case DB_ATTRTYPE_DATETIMN:  
        case DB_ATTRTYPE_TIMESTAMP:
        case DB_ATTRTYPE_SMALLDATETIME:
        case DB_ATTRTYPE_DATE:
          return 'util.Date';
          
        case DB_ATTRTYPE_BINARY:
        case DB_ATTRTYPE_CHAR:
        case DB_ATTRTYPE_IMAGE:
        case DB_ATTRTYPE_NCHAR:  
        case DB_ATTRTYPE_NVARCHAR:
        case DB_ATTRTYPE_TEXT:
        case DB_ATTRTYPE_VARBINARY:
        case DB_ATTRTYPE_VARCHAR:
        case DB_ATTRTYPE_ENUM:
          return 'string';
          
        case DB_ATTRTYPE_DECIMAL:
        case DB_ATTRTYPE_DECIMALN:
        case DB_ATTRTYPE_NUMERIC:  
        case DB_ATTRTYPE_NUMERICN:
          return $this->scale == 0 ? 'int' : 'float';
          
        case DB_ATTRTYPE_INT:
        case DB_ATTRTYPE_INTN:
        case DB_ATTRTYPE_TINYINT:
        case DB_ATTRTYPE_SMALLINT:
          return 'int';
          
        case DB_ATTRTYPE_FLOAT:
        case DB_ATTRTYPE_FLOATN:
        case DB_ATTRTYPE_MONEY:
        case DB_ATTRTYPE_MONEYN:
        case DB_ATTRTYPE_SMALLMONEY:
        case DB_ATTRTYPE_REAL:
          return 'float';
          
        case DB_ATTRTYPE_SYSNAME:
          return 'string';
      }
      
      return FALSE;
    }
  }
?>
