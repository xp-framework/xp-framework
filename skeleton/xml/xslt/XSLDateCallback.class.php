<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * XSL callbacks for Date operations
   *
   * @ext       ext/date
   * @see       xp://util.Date
   * @purpose   XSL callback
   */
  class XSLDateCallback extends Object {
  
    /**
     * Format the given date with the format specifier
     *
     * @param   string date
     * @param   string format
     * @param   string timezone default NULL
     * @return  string
     */
    #[@xslmethod]
    public function format($date, $format, $timezone= NULL) {
      return create(new Date($date))->toString($format);
    }
  }
?>
