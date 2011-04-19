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
    protected $prop= NULL;
    protected $descr= NULL;
    
    /**
     * Constructor
     *
     * @param   util.Properties prop
     */
    public function __construct(Properties $prop) {
      $this->prop= $prop;
      $this->descr= $this->prop->readString('this', 'description', 'Tests');
    }

    /**
     * Get all test cases
     *
     * @param   var[] arguments
     * @return  unittest.TestCase[]
     */
    public function testCasesWith($arguments) {
      $r= array();
      $section= $this->prop->getFirstSection();
      do {
        if ('this' == $section) continue;   // Ignore special section
        $r= array_merge($r, $this->testCasesInClass(
          XPClass::forName($this->prop->readString($section, 'class')),
          $arguments ? $arguments : $this->prop->readArray($section, 'args')
        ));
      } while ($section= $this->prop->getNextSection());
      return $r;
    }

    /**
     * Creates a string representation of this source
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'['.$this->descr.' @ '.$this->prop->getFilename().']';
    }
  }
?>
