<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.Enum');
  
  /**
   * Profiling enumeration
   * 
   * @purpose  Demo
   */
  class Profiling extends Enum {
    public static
      $INSTANCE,
      $EXTENSION;
    
    public static
      $fixture = NULL;
    
    static function __static() {
      self::$INSTANCE= new self(0, 'INSTANCE');
      self::$EXTENSION= new self(1, 'EXTENSION');
    }

    /**
     * Returns all enum members
     *
     * @return  lang.Enum[]
     */
    public static function values() {
      return parent::membersOf(__CLASS__);
    }
  }
?>
