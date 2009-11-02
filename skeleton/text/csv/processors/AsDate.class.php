<?php
/* This class is part of the XP framework
 *
 * $Id: AsDate.class.php 11504 2009-09-15 13:36:13Z friebe $ 
 */

  uses('text.csv.CellProcessor', 'util.Date');

  /**
   * Returns cell values as a date objects
   *
   * @test    xp://net.xp_framework.unittest.text.csv.CellProcessorTest
   * @see     xp://text.csv.CellProcessor
   */
  class AsDate extends CellProcessor {
    protected $default= NULL;

    /**
     * Set default when empty columns are encountered
     *
     * @param   util.Date default
     * @return  text.csv.processors.AsDate
     */
    public function withDefault(Date $default= NULL) {
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
      if ('' !== $in) {
        try {
          $date= new Date($in);
        } catch (IllegalArgumentException $e) {
          throw new FormatException($e->getMessage());
        }
      } else if (NULL === $this->default) {
        throw new FormatException('Cannot parse empty date');
      } else {
        $date= $this->default;
      }
      return $this->proceed($date);
    }
  }
?>
