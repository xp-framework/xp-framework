<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.unittest.sources.AbstractSource', 'util.Properties');

  /**
   * Source that load tests from a .ini file
   *
   * @purpose  Source implementation
   */
  class PropertySource extends xp·unittest·sources·AbstractSource {
    protected
      $prop= NULL;
    
    /**
     * Constructor
     *
     * @param   util.Properties prop
     */
    public function __construct(Properties $prop) {
      $this->prop= $prop;
    }

    /**
     * Get all test classes
     *
     * @return  util.collections.HashTable<lang.XPClass, lang.types.ArrayList>
     */
    public function testClasses() {
      $tests= new HashTable();
      $section= $this->prop->getFirstSection();
      do {
        if ('this' == $section) continue;   // Ignore special section

        $tests->put(
          XPClass::forName($this->prop->readString($section, 'class')),
          new ArrayList($this->prop->readArray($section, 'args'))
        );
      } while ($section= $this->prop->getNextSection());
      
      return $tests;
    }
  }
?>
