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
   *   $d= &DateParser::parse('13.02');
   *   echo $d->toString();
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
     * @throws  lang.FormatException in case the date could not be parsed
     */
    function &parse($s) {
      if (empty($s)) {
      
        // Border case
        return throw(new FormatException('Cannot parse empty string'));
      } elseif (preg_match('/^([0-9]+)\.([0-9]+)(\.([0-9]+))? ?([0-9]+)?:?([0-9]+)?:?([0-9]+)?/', $s, $matches)) {
      
        // German date format
        $stamp= mktime(
          isset($matches[5]) ? $matches[5] : 0, 
          isset($matches[6]) ? $matches[6] : 0, 
          isset($matches[7]) ? $matches[7] : 0, 
          $matches[2], 
          $matches[1], 
          isset($matches[4]) ? $matches[4] : date('Y')
        );
      } else {
      
        // FIXME: strtotime() returns -1 on failure, but also for the date
        // Jan 01 1970 00:59:59 (in case the underlying OS supports negative
        // timestamps). Unfortunately, no warnings are issued whatsoever, so
        // there is no way to find out if this is the case or if the function 
        // actually failed.
        //
        // I would consider this a bug in PHP (the function should return FALSE
        // or at least raise a warning), but for now, we will have to live 
        // with it.
        if (-1 === ($stamp= strtotime(strtolower($s)))) {
          return throw(new FormatException('Could not parse "'.$s.'"'));
        }
      }
      
      return new Date($stamp);
    }
  }
?>
