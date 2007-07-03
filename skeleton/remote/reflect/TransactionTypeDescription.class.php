<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * TransactionTypeDescription enumeration.
   *
   * @purpose  Enumeration
   */
  class TransactionTypeDescription extends Object {
    public static
      $NOT_SUPPORTED,
      $REQUIRED,
      $SUPPORTS,
      $REQUIRES_NEW,
      $MANDATORY,
      $NEVER,
      $UNKNOWN;

    private
      $ordinal  = 0;

    public 
      $name     = '';

    static function __static() {
      self::$NOT_SUPPORTED= new self(0, 'NOT_SUPPORTED');
      self::$REQUIRED= new self(1, 'REQUIRED');
      self::$SUPPORTS= new self(2, 'SUPPORTS');
      self::$REQUIRES_NEW= new self(3, 'REQUIRES_NEW');
      self::$MANDATORY= new self(4, 'MANDATORY');
      self::$NEVER= new self(5, 'NEVER');
      self::$UNKNOWN= new self(6, 'UNKNOWN');
    }
      
    /**
     * Constructor
     *
     * @param   int ordinal default 0
     * @param   string name default ''
     */
    public function __construct($ordinal= 0, $name= '') {
      $this->ordinal= $ordinal;
      $this->name= $name;
    }
    
    /**
     * Retrieve a TransactionTypeDescription by its name
     *
     * @param   string name
     * @return  remote.reflect.TransactionTypeDescription
     */
    public static function valueOf($name) {
      try {
        $r= new ReflectionClass(__CLASS__);
        return $r->getStaticPropertyValue($name);
      } catch (ReflectionException $e) {
        throw new IllegalArgumentException($name.': '.$e->getMessage());
      }
    }
  }
?>
