<?php
/* This class is part of the XP framework
 *
 * $Id: AsDouble.class.php 11504 2009-09-15 13:36:13Z friebe $ 
 */

  uses('text.csv.CellProcessor');

  /**
   * Returns cell values as an integer
   *
   * @test    xp://net.xp_framework.unittest.text.csv.CellProcessorTest
   * @see     xp://text.csv.CellProcessor
   */
  class AsDouble extends CellProcessor {

    /**
     * Processes cell value
     *
     * @param   var in
     * @return  var
     * @throws  lang.FormatException
     */
    public function process($in) {
      if (1 !== sscanf($in, '%f', $out)) {
        throw new FormatException('Cannot parse "'.$in.'" into an double');
      }
      return $this->proceed($out);
    }
  }
?>
