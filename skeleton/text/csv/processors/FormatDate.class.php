<?php
/* This class is part of the XP framework
 *
 * $Id: FormatDate.class.php 11504 2009-09-15 13:36:13Z friebe $
 */

  uses('text.csv.CellProcessor', 'util.Date');

  /**
   * Formats dates as cell values
   *
   * @test    xp://net.xp_framework.unittest.text.csv.CellProcessorTest
   * @see     xp://text.csv.CellProcessor
   */
  class FormatDate extends CellProcessor {
    protected $format= '';
    protected $default= NULL;

    /**
     * Creates a new date formatter
     *
     * @see     xp://util.Date#toString for format string composition
     * @param   string format
     * @param   text.csv.CellProcessor if omitted, no further processing will be done
     */
    public function __construct($format, CellProcessor $next= NULL) {
      parent::__construct($next);
      $this->format= $format;
    }

    /**
     * Set default when empty columns are encountered
     *
     * @param   util.Date default
     * @return  text.csv.processors.FormatDate
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
      if (NULL === $in && NULL !== $this->default) {
        $date= $this->default;
      } else if (!$in instanceof Date) {
        throw new FormatException('Cannot format non-date '.xp::stringOf($in));
      } else {
        $date= $in;
      }
      return $this->proceed($date->toString($this->format));
    }
  }
?>
