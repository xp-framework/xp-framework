<?php
/* This class is part of the XP framework
 *
 * $Id: Optional.class.php 11504 2009-09-15 13:36:13Z friebe $
 */

  uses('text.csv.CellProcessor');

  /**
   * Returns a default value if an empty string is encountered.
   *
   * @test    xp://net.xp_framework.unittest.text.csv.CellProcessorTest
   * @see     xp://text.csv.Required
   * @see     xp://text.csv.CellProcessor
   */
  class Optional extends CellProcessor {
    protected $default= NULL;

    /**
     * Set default when empty columns are encountered
     *
     * @param   var default
     * @return  text.csv.processors.Optional
     */
    public function withDefault($default) {
      $this->default= $default;
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
      if ('' === $in || NULL === $in) {
        return $this->default;
      }
      return $this->proceed($in);
    }
  }
?>
