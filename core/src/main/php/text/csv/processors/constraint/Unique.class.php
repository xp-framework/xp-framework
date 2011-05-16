<?php
/* This class is part of the XP framework
 *
 * $Id: Unique.class.php 11504 2009-09-15 13:36:13Z friebe $
 */

  uses('text.csv.CellProcessor');

  /**
   * Throws an exception if a value is encountered more than once.
   *
   * @test    xp://net.xp_framework.unittest.text.csv.CellProcessorTest
   * @see     xp://text.csv.CellProcessor
   */
  class Unique extends CellProcessor {
    protected $values= array();

    /**
     * Processes cell value
     *
     * @param   var in
     * @return  var
     * @throws  lang.FormatException
     */
    public function process($in) {
      if (isset($this->values[$in])) {
        throw new FormatException('Value "'.$in.'" already encountered');
      }
      $this->values[$in]= TRUE;
      return $this->proceed($in);
    }
  }
?>
