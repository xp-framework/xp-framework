<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Parses dates
   *
   * <code>
   *   uses('text.parser.DateParser');
   *
   *   echo DateParser::parse('13.02')->toString();
   * </code>
   *
   * @see      http://www.gnu.org/software/tar/manual/html_chapter/tar_7.html
   * @purpose  Parser
   */
  class DateParser extends Object {
  
    /**
     * Parse a date
     *
     * @model   static
     * @access  public
     * @param   string s
     * @return  &util.Date
     */
    public static function parse($s) {
      if (preg_match('/^([0-9]+)\.([0-9]+)(\.([0-9]+))? ?([0-9]+)?:?([0-9]+)?:?([0-9]+)?/', $s, $matches)) {
        $stamp= mktime(
          isset($matches[5]) ? $matches[5] : 0, 
          isset($matches[6]) ? $matches[6] : 0, 
          isset($matches[7]) ? $matches[7] : 0, 
          $matches[2], 
          $matches[1], 
          isset($matches[4]) ? $matches[4] : date('Y')
        );
      } else {
        $stamp= strtotime(strtolower($s));
      }
      
      return new Date($stamp);
    }
  }
?>
