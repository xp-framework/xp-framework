<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Status enumeration for 
   *
   * @see      xp://net.xp_framework.db.caffeine.Rfc
   * @purpose  Enumeration
   */
  class RfcStatus extends Enum {
    public static
      $draft,
      $discussion,
      $implemented,
      $rejected,
      $obsoleted;
    
    static function __static() {
      self::$draft= new self(500, 'draft');
      self::$discussion= new self(10000, 'discussion');
      self::$implemented= new self(20000, 'implemented');
      self::$rejected= new self(30000, 'rejected');
      self::$obsoleted= new self(30001, 'obsoleted');
    }
    
    /**
     * Return this status' numerical id
     *
     * @return  int
     */
    public function id() {
      return $this->ordinal;
    }

    /**
     * Returns all enum members
     *
     * @return  lang.Enum[]
     */
    public static function values() {
      return parent::membersOf(__CLASS__);
    }
    
    /**
     * Get a status for a given ID
     *
     * @param   int id
     * @return  net.xp_framework.db.caffeine.RfcStatus
     * @throws  lang.IllegalArgumentException
     */
    public static function forId($id) {
      static $map;
      
      // Lazily initialize map
      if (!$map) $map= array(
        500   => self::$draft,
        10000 => self::$discussion,
        20000 => self::$implemented,
        30000 => self::$rejected,
        30001 => self::$obsoleted
      );
      
      // Sanity check
      if (!isset($map[$id])) {
        throw new IllegalArgumentException('No such status #'.$id);
      }
      return $map[$id];
    }
  }
?>
