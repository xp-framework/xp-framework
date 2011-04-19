<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'lang.FormatException');

  /**
   * Parses dates
   *
   * <code>
   *   uses('text.parser.DateParser');
   *
   *   $d= DateParser::parse('13.02');
   *   echo $d->toString();
   *    
   *   $d= DateParser::parse('200401190957+0100');
   *   echo $d->toString();
   *
   *   $d= DateParser::parse('20040414101045Z');
   *   echo $d->toString();
   *
   * </code>
   *
   * @see      http://www.gnu.org/software/tar/manual/html_chapter/tar_7.html
   * @see      http://ldap.akbkhome.com/syntax/1%2E3%2E6%2E1%2E4%2E1%2E1466%2E115%2E121%2E1%2E24.html
   * @purpose  Parser
   */
  class DateParser extends Object {

    /**
     * Parse a date
     *
     * @param   string s
     * @return  util.Date
     * @throws  lang.FormatException in case the date could not be parsed
     */
    public static function parse($s) {
      if (empty($s)) {
      
        // Border case
        throw new FormatException('Cannot parse empty string');
      }
      
      try {
        return new Date($s);
      } catch (IllegalArgumentException $e) {
        throw new FormatException($e->getMessage());
      }
    }
  }
?>
