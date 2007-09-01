<?php
/* This class is part of the XP framework
 *
 * $Id: FieldType.class.php 9438 2007-02-08 11:48:11Z olli $ 
 */

  namespace rdbms;

  /**
   * Field type constants
   *
   * @purpose  Enumeration
   */
  class FieldType extends lang::Object {
    const BINARY =         0x0000;             
    const BIT =            0x0001;               
    const CHAR =           0x0002;              
    const DATETIME =       0x0003;            
    const DATETIMN =       0x0004;            
    const DECIMAL =        0x0005;             
    const DECIMALN =       0x0006;            
    const FLOAT =          0x0007;             
    const FLOATN =         0x0008;            
    const IMAGE =          0x0009;             
    const INT =            0x000A;               
    const INTN =           0x000B;              
    const MONEY =          0x000C;             
    const MONEYN =         0x000D;            
    const NCHAR =          0x000E;             
    const NUMERIC =        0x000F;             
    const NUMERICN =       0x0010;            
    const NVARCHAR =       0x0011;            
    const REAL =           0x0012;              
    const SMALLDATETIME =  0x0013;         
    const SMALLINT =       0x0014;         
    const SMALLMONEY =     0x0015;       
    const SYSNAME =        0x0016;          
    const TEXT =           0x0017;           
    const TIMESTAMP =      0x0018;        
    const TINYINT =        0x0019;          
    const VARBINARY =      0x001A;        
    const VARCHAR =        0x001B;          
    const ENUM =           0x001C;          
    const DATE =           0x001D;
  }
?>
