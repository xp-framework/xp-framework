<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.FieldType');

  /**
   * Maps rdbms field types to input types and casters.
   *
   * @see      xp://rdbms.FieldType
   * @purpose  Utility class
   */
  class FieldTypeMap extends Object {
  
    /**
     * Map field type to input type
     *
     * @param   int fieldType of one of FieldType's class constants
     * @return  string input type
     */
    public static function typeOf($fieldType) {
      static $types= array(
        FieldType::BINARY        => 'core:string',   
        FieldType::BIT           => 'core:number',     
        FieldType::CHAR          => 'core:string',    
        FieldType::DATETIME      => 'core:date',  
        FieldType::DATETIMN      => 'core:date',  
        FieldType::DECIMAL       => 'core:number',   
        FieldType::DECIMALN      => 'core:number',  
        FieldType::FLOAT         => 'core:number',   
        FieldType::FLOATN        => 'core:number',   
        FieldType::IMAGE         => 'core:string',   
        FieldType::INT           => 'core:number',     
        FieldType::INTN          => 'core:number',    
        FieldType::MONEY         => 'core:string',   
        FieldType::MONEYN        => 'core:string',   
        FieldType::NCHAR         => 'core:string',   
        FieldType::NUMERIC       => 'core:number',   
        FieldType::NUMERICN      => 'core:number',  
        FieldType::NVARCHAR      => 'core:string',  
        FieldType::REAL          => 'core:number',    
        FieldType::SMALLDATETIME => 'core:date',
        FieldType::SMALLINT      => 'core:number',  
        FieldType::SMALLMONEY    => 'core:string',
        FieldType::SYSNAME       => 'core:string',   
        FieldType::TEXT          => 'core:text',    
        FieldType::TIMESTAMP     => 'core:string', 
        FieldType::TINYINT       => 'core:number',   
        FieldType::VARBINARY     => 'core:string', 
        FieldType::VARCHAR       => 'core:string', 
      );
      
      return $types[$fieldType];
    }
    
    /**
     * Map field type to caster spec
     *
     * @param   int fieldType of one of FieldType's class constants
     * @return  mixed[] caster
     */
    public static function casterFor($fieldType) {
      static $casters= array(
        FieldType::BINARY        => NULL,   
        FieldType::BIT           => NULL,     
        FieldType::CHAR          => NULL,    
        FieldType::DATETIME      => array('scriptlet.xml.workflow.casters.ToDate'),  
        FieldType::DATETIMN      => array('scriptlet.xml.workflow.casters.ToDate'),  
        FieldType::DECIMAL       => array('scriptlet.xml.workflow.casters.ToFloat'),   
        FieldType::DECIMALN      => array('scriptlet.xml.workflow.casters.ToFloat'),  
        FieldType::FLOAT         => array('scriptlet.xml.workflow.casters.ToFloat'),
        FieldType::FLOATN        => NULL,   
        FieldType::IMAGE         => NULL,   
        FieldType::INT           => array('scriptlet.xml.workflow.casters.ToInteger'),     
        FieldType::INTN          => array('scriptlet.xml.workflow.casters.ToInteger'),    
        FieldType::MONEY         => NULL,   
        FieldType::MONEYN        => NULL,   
        FieldType::NCHAR         => NULL,   
        FieldType::NUMERIC       => array('scriptlet.xml.workflow.casters.ToInteger'),   
        FieldType::NUMERICN      => array('scriptlet.xml.workflow.casters.ToInteger'),  
        FieldType::NVARCHAR      => NULL,  
        FieldType::REAL          => array('scriptlet.xml.workflow.casters.ToFloat'),    
        FieldType::SMALLDATETIME => array('scriptlet.xml.workflow.casters.ToDate'),
        FieldType::SMALLINT      => array('scriptlet.xml.workflow.casters.ToInteger'),  
        FieldType::SMALLMONEY    => NULL,
        FieldType::SYSNAME       => NULL,   
        FieldType::TEXT          => NULL,    
        FieldType::TIMESTAMP     => NULL, 
        FieldType::TINYINT       => array('scriptlet.xml.workflow.casters.ToInteger'),
        FieldType::VARBINARY     => NULL, 
        FieldType::VARCHAR       => NULL, 
      );

      return $casters[$fieldType];
    }
  
  }
?>
