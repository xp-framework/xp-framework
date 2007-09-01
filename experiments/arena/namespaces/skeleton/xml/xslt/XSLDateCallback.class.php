<?php
/* This class is part of the XP framework
 *
 * $Id: XSLDateCallback.class.php 10987 2007-08-29 08:10:32Z kiesel $ 
 */

  namespace xml::xslt;

  uses('util.Date');

  /**
   * XSL callbacks for Date operations
   *
   * @ext       ext/date
   * @see       xp://util.Date
   * @purpose   XSL callback
   */
  class XSLDateCallback extends lang::Object {
  
    /**
     * Format the given date with the format specifier
     *
     * @param   string date
     * @param   string format
     * @param   string timezone default NULL
     * @return  string
     */
    #[@xslmethod]
    public function format($date, $format, $timezone= ) {
      return create(new util::Date($date))->toString($format);
    }
  }
?>
