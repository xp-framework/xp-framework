<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.unittest.sources';

  uses('util.collections.HashTable', 'lang.types.ArrayList');

  /**
   * Source
   *
   * @purpose  Abstract base class
   */
  abstract class xp·unittest·sources·AbstractSource extends Object {

    /**
     * Get all test classes
     *
     * @return  util.collections.HashTable<lang.XPClass, lang.types.ArrayList>
     */
    public abstract function testClasses();
  }
?>
