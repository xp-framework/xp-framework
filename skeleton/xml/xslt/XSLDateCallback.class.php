<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'util.DateMath', 'util.TimeInterval');

  /**
   * XSL callbacks for Date operations
   *
   * @ext       ext/date
   * @see       xp://util.Date
   * @see       xp://util.DateMath
   * @test      xp://net.xp_framework.unittest.xml.XslCallbackTest
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
      $timezone= empty($timezone) ? NULL : $timezone;
      return create(new Date($date))->toString($format, new TimeZone($timezone));
    }
    
    /**
     * Diff two dates with the given interval
     *
     * @param   string type
     * @param   string strdate1
     * @param   string strdate2
     * @return  int
     */
    #[@xslmethod]
    public function diff($type, $strdate1, $strdate2) {
      return DateMath::diff(
        Enum::valueOf(XPClass::forName('util.TimeInterval'), strtoupper($type)),
        new Date($strdate1),
        new Date($strdate2)
      );
    }
  }
?>
