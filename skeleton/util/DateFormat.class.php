<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Date format
   *
   * @see      php://strftime
   * @test     xp://net.xp_framework.unittest.util.DateFormatTest
   */
  class DateFormat extends Object {
    protected $format= '';
    
    /**
     * (Insert method's description here)
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
            case 'Y': $parsed['year']= substr($in, $o, 4); $o+= 4; break;     // Four digit representation for the year
            case 'm': $parsed['month']= substr($in, $o, 2); $o+= 2; break;    // Two digit representation of the month
            case 'd': $parsed['day']= substr($in, $o, 2); $o+= 2; break;      // Two-digit day of the day
            case 'H': $parsed['hour']= substr($in, $o, 2); $o+= 2; break;     // Two digit representation of the hour in 24-hour format
            case 'M': $parsed['minute']= substr($in, $o, 2); $o+= 2; break;   // Two digit representation of the minute
            case 'S': $parsed['second']= substr($in, $o, 2); $o+= 2; break;   // Two digit representation of the second
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
