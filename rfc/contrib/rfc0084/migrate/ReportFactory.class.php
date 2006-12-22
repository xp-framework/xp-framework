<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('REPORT_TYPE_TEXT',  'text');
  define('REPORT_TYPE_HTML',  'html');
  
  uses('HtmlReport', 'TextReport');

  /**
   * Creates reports objects
   *
   * @see      xp://Report
   * @purpose  Factory
   */
  class ReportFactory extends Object {

    /**
     * Factory method
     *
     * @model   static
     * @access  public
     * @param   string type
     * @return  &Report 
     * @throws  lang.IllegalArgumentException in case an error occurs
     */
    function factory($type) {  
      switch ($type) {
        case REPORT_TYPE_TEXT: case 't': return new TextReport();
        case REPORT_TYPE_HTML: case 'h': return new HtmlReport();
        default: return throw(new IllegalArgumentException('Unknown report type "'.$type.'"'));
      }
    }
  }
?>
