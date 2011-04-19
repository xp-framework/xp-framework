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
   * --------
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
   * Format string
   * -------------
   * The format string consists of format tokens preceded by a percent
   * sign (%) and any other character. The following format tokens are 
   * supported:
   * <ul>
   *   <li>%Y - Four digit representation for the year</li>
   *   <li>%m - Two digit representation for the month (01= Jan, 12= Dec)</li>
   *   <li>%d - Two digit representation for the day</li>
   *   <li>%H - Two digit representation for the hour in 24-hour format</li>
   *   <li>%I - Two digit representation for the hour in 12-hour format</li>
   *   <li>%M - Four digit representation for the minute</li>
   *   <li>%S - Four digit representation for the second</li>
   *   <li>%p - AM or PM - use together with %I</li>
   *   <li>%z - Timezone name, in the form Region/City</li>
   *   <li>%Z - Timezone offset, e.g. -0800</li>
   * </li>
   *
   * Furthermore, a mapping may be notated as follows:
   * <pre>
   *   %[month=Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec]
   *     ^^^^^ ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
   *     |     Values (1..n)
   *     Variable
   * </pre>
   * When parsing and one of the strings is encountered, it is mapped to
   * the position it is notated at, e.g. "Jan" inside the input string
   * parsed would yield 1 in the date's month field. When formatting, the
   * date's field referenced by the variable if used to look up the value
   * in the map, concatenating it to the output string.
   *
   * @see      php://strftime
   * @see      php://strptime
   * @test     xp://net.xp_framework.unittest.text.DateFormatTest
   */
  class DateFormat extends Object {
    protected $format= array();
    
    /**
     * Constructor
     *
     * @param   string format
     * @throws  lang.IllegalArgumentException if the format string is malformed
     */
    public function __construct($format) {
      for ($i= 0, $s= strlen($format); $i < $s; $i++) {
        if ('%' === $format{$i}) {
          $i++;
          switch ($format{$i}) {
            case '%': {   // Literal percent
              $this->format[]= '%'; 
              break;
            }
            case '[': {   // Map
              $end= strpos($format, ']', $i);
              list($var, $values)= explode('=', substr($format, $i+ 1, $end- $i- 1), 2);
              $i= $end;
              $this->format[]= array($var, explode(',', $values));
              break;
            }
            default: {    // Any other character - verify it's supported
              if (!strspn($format{$i}, 'YmdHIMSpzZ')) {
                throw new IllegalArgumentException('Unknown format token "'.$format{$i}.'"');
              }
              $this->format[]= '%'.$format{$i};
            }
          }
        } else {
          $this->format[]= $format{$i};
        }
      }
    }
    
    /**
     * Formats a 
     *
     * @param   util.Date d
     * @return  string
     */
    public function format(Date $d) {
      $out= '';
      foreach ($this->format as $token) {
        switch ($token) {
          case '%Y': $out.= $d->getYear(); break;
          case '%m': $out.= str_pad($d->getMonth(), 2, '0', STR_PAD_LEFT); break;
          case '%d': $out.= str_pad($d->getDay(), 2, '0', STR_PAD_LEFT); break;
          case '%H': $out.= str_pad($d->getHours(), 2, '0', STR_PAD_LEFT); break;
          case '%M': $out.= str_pad($d->getMinutes(), 2, '0', STR_PAD_LEFT); break;
          case '%S': $out.= str_pad($d->getSeconds(), 2, '0', STR_PAD_LEFT); break;
          case '%p': $h= $d->getHours(); $out.= $h >= 12 ? 'PM': 'AM'; break;
          case '%I': $out.= str_pad($d->getHours() % 12, 2, '0', STR_PAD_LEFT); break;
          case '%z': $out.= $d->getTimeZone()->getName(); break;
          case '%Z': $out.= $d->getOffset(); break;
          case is_array($token): $out.= $token[1][call_user_func(array($d, 'get'.$token[0]))- 1]; break;
          default: $out.= $token;
        }
      }
      return $out;
    }
    
    /**
     * Parses an n-digit number from the given input string and returns
     * it as an integer. The number may be prefixed by leading zeros.
     *
     * @param   string in
     * @param   int l digits
     * @param   int o offset
     * @return  int
     * @throws  lang.FormatException
     */
    protected function parseNumber($in, $l, $o) {
      if (!is_numeric($n= substr($in, $o, $l))) {
        throw new FormatException('Expected a '.$l.' digit number, have "'.$n.'..." at offset '.$o);
      }
      return (int)$n;
    }
    
    /**
     * Parses an input string
     *
     * @param   string in
     * @return  util.Date
     */
    public function parse($in) {
      $o= 0;
      $m= $tz= $go= NULL; $il= strlen($in);
      $parsed= array('year' => 0, 'month' => 0, 'day' => 0, 'hour' => 0, 'minute' => 0, 'second' => 0);
      foreach ($this->format as $token) {
        switch ($token) {
          case '%Y': $parsed['year']= $this->parseNumber($in, 4, $o); $o+= 4; break;
          case '%m': $parsed['month']= $this->parseNumber($in, 2, $o); $o+= 2; break;
          case '%d': $parsed['day']= $this->parseNumber($in, 2, $o); $o+= 2; break;
          case '%H': $parsed['hour']= $this->parseNumber($in, 2, $o); $o+= 2; break;
          case '%M': $parsed['minute']= $this->parseNumber($in, 2, $o); $o+= 2; break;
          case '%S': $parsed['second']= $this->parseNumber($in, 2, $o); $o+= 2; break;
          case '%p': $m= substr($in, $o, 2); $o+= 2; break;
          case '%I': $parsed['hour']= $this->parseNumber($in, 2, $o); $o+= 2; break;
          case '%z': $tz= new TimeZone($n= substr($in, $o, strcspn($in, ' ', $o))); $o+= strlen($n); break;
          case '%Z': {
            sscanf(substr($in, $o, 5), '%c%02d%02d', $sign, $hours, $minutes); 
            $fa= '-' === $sign ? 1 : -1;    // -0800 means 8 hours ahead of time
            $go= array('hour' => $fa * $hours, 'minute'  => $fa * $minutes);
            $tz= new TimeZone('GMT');
            $o+= 5; 
            break;
          }
          case is_array($token): {
            foreach ($token[1] as $i => $value) {
              if ($value !== substr($in, $o, strlen($value))) continue;
              $parsed[$token[0]]= $i+ 1;
              $o+= strlen($value);
              break 2;
            }
            throw new FormatException('Expected ['.implode(', ', $token[1]).'] but have "'.substr($in, $o, 5).'..."');
          }
          default: {
            if ($o >= $il) {
              throw new FormatException('Not enough input at offset '.$o);
            }
            if ($token !== $in{$o}) {
              throw new FormatException('Expected "'.$token.'" but have "'.$in{$o}.'" at offset '.$o);
            }
            $o++;
          }
        }
      }
      
      // AM/PM calculation
      if ('AM' === $m && 12 === $parsed['hour']) {
        $parsed['hour']= 0;
      } else if ('PM' === $m && 12 !== $parsed['hour']) {
        $parsed['hour']+= 12;
      }
      
      // Timezone offset calculationn
      if ($go) {
        $parsed['hour']+= $go['hour'];
        $parsed['minute']+= $go['minute'];
      }
      
      // echo "$in => "; var_dump($parsed);
      return Date::create(
        $parsed['year'], 
        $parsed['month'], 
        $parsed['day'], 
        $parsed['hour'], 
        $parsed['minute'], 
        $parsed['second'],
        $tz
      );
    }
    
    /**
     * Creates a string representation of this date format instance
     *
     * @return  string
     */
    public function toString() {
      $str= '';
      foreach ($this->format as $token) {
        if (is_array($token)) {
          $str.= '['.$token[0].'='.implode(',', $token[1]).']';
        } else {
          $str.= $token;
        }
      }
      return $this->getClassName().'<"'.$str.'">';
    }
  }
?>
