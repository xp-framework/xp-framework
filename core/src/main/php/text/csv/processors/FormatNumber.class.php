<?php
/* This class is part of the XP framework
 *
 * $Id: FormatEnum.class.php 11504 2009-09-15 13:36:13Z friebe $
 */

  uses('text.csv.CellProcessor');

  /**
   * Formats a given number given the formatting options
   *
   * @test    xp://net.xp_framework.unittest.text.csv.CellProcessorTest
   * @see     xp://text.csv.CellProcessor
   */
  class FormatNumber extends CellProcessor {
    protected
      $decimals           = 2,
      $decimalPoint       = '.',
      $thousandsSeparator = '';
    
    /**
     * Set formatting options
     *
     * @param   int decimals default 2
     * @param   string decimalPoint default "."
     * @param   string thousandsSeparator default ""
     * @return  text.csv.processors.FormatNumber
     */
    public function withFormat($decimals= 2, $decimalPoint= '.', $thousandsSeparator= '') {
      $this->decimals= $decimals;
      $this->decimalPoint= $decimalPoint;
      $this->thousandsSeparator= $thousandsSeparator;
      return $this;
    }

    /**
     * Processes cell value
     *
     * @param   var in
     * @return  var
     * @throws  lang.FormatException
     */
    public function process($in) {
      if (!(NULL === $in || is_numeric($in))) throw new FormatException('Cannot format non-number '.xp::stringOf($in));
      return $this->proceed(number_format($in, $this->decimals, $this->decimalPoint, $this->thousandsSeparator));
    }
  }
?>
