<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Represents a date format and allows converting dates to it as well 
   * as parsing them from it.
   *
   * Examples
   * ---------
   * Formatting a date:
   * <code>
   *   $format= new DateFormat('%d.%m.%Y');
   *   $string= $format->format(new Date('2009-12-14')); // "14.12.2009"
   * </code>
   *
   * Parsing a date from a string:
   * <code>
   *   $format= new DateFormat('%d.%m.%Y');
   *   $date= $format->parse('14.12.2009');
   * </code>
   *
   * Format tokens
   * -------------
   * The following format tokens may be used:
   * <ul>
   *   <li>%Y - Four digit representation for the year</li>
   *   <li>%m - Two digit representation for the month (01= Jan, 12= Dec)</li>
   *   <li>%d - Two digit representation for the day</li>
   *   <li>%H - Two digit representation for the hour in 24-hour format</li>
   *   <li>%M - Four digit representation for the minute</li>
   *   <li>%S - Four digit representation for the second</li>
   * </li>
   *
   * @see      php://strftime
   * @see      php://strptime
   * @test     xp://net.xp_framework.unittest.util.DateFormatTest
   */
  class DateFormat extends Object {
    protected $format= '';
    
    /**
     * Constructor
     *
     * @param   string format
     */
    public function __construct($format) {
      $this->format= $format;
    }
    
    /**
     * Formats a 
     *
     * @param   util.Date d
     * @return  string
     */
    public function format(Date $d) {
      return $d->format($this->format);
    }
    
    /**
     * Parses an input string
     *
     * @param   string in
     * @return  util.Date
     */
    public function parse($in) {
      $o= 0;
      $parsed= array('year' => 0, 'month' => 0, 'day' => 0, 'hour' => 0, 'minute' => 0, 'second' => 0);
      for ($i= 0, $s= strlen($this->format); $i < $s; $i++) {
        if ('%' === $this->format{$i}) {
          $i++;
          switch ($this->format{$i}) {
            case 'Y': $parsed['year']= substr($in, $o, 4); $o+= 4; break;
            case 'm': $parsed['month']= substr($in, $o, 2); $o+= 2; break;
            case 'd': $parsed['day']= substr($in, $o, 2); $o+= 2; break;
            case 'H': $parsed['hour']= substr($in, $o, 2); $o+= 2; break;
            case 'M': $parsed['minute']= substr($in, $o, 2); $o+= 2; break;
            case 'S': $parsed['second']= substr($in, $o, 2); $o+= 2; break;
            case '*': {
              $o+= strpos($in, $this->format{++$i}, $o)+ 1; 
              break;
            }
            case '[': {
              $end= strpos($this->format, ']', $i);
              sscanf(substr($this->format, $i+ 1, $end- $i- 1), "%[^=]=%[^\r]", $var, $map);
              foreach (explode(',', $map) as $j => $n) {
                if ($n !== substr($in, $o, strlen($n))) continue;
                $parsed[$var]= $j+ 1;
                $o+= strlen($n);
                $i= $end;
                break 2;
              }
              throw new FormatException('Expected ['.$map.'] but have "'.substr($in, $o, 5).'[...]"');
            }
            default: {
              throw new IllegalArgumentException('Unknown format token "'.$this->format{$i}.'"');
            }
          }
        } else if ($this->format{$i} !== $in{$o}) {
          throw new FormatException('Expected "'.$this->format{$i}.'" but have "'.$in{$o}.'" at offset '.$o);
        } else {
          $o++;
        }
      }
      // echo "$in => "; var_dump($parsed);
      return Date::create($parsed['year'], $parsed['month'], $parsed['day'], $parsed['hour'], $parsed['minute'], $parsed['second']);
    }
  }
?>
