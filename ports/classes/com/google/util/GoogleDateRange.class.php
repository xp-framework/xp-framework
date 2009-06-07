<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Helps constructing a date range
   *
   * If you want to limit your results to documents that were published 
   * within a specific date range, then you can use the "daterange:" 
   * query term to accomplish this. 
   *
   * The "daterange:" query term must be in the following format:
   * <pre>
   *   daterange:<start_date>-<end date> 
   * </pre>
   * where
   * <pre>
   *   <start_date> = Julian date indicating the start of the date range
   *   <end date> = Julian date indicating the end of the date range 
   * </pre>
   *
   * Example:
   * <code>
   *   uses('com.google.util.GoogleDateRange');
   *
   *   $query= 'Google';
   *   with ($range= GoogleDateRange::forDates(
   *     Date::fromString('Dec 14 2003'), 
   *     Date::now())
   *   ); {
   *     $query.= ' '.$range->toString();
   *   }
   *
   *   var_dump($query);    // "Google daterange:2452988-2453035"
   * </code>
   * 
   * @see      http://www.google.com/apis/reference.html#2_2
   * @purpose  Helper class
   */
  class GoogleDateRange extends Object {
    public
      $start    = NULL,
      $end      = NULL;
      
    /**
     * Returns a date range for the given start and end dates
     *
     * @param   util.Date start
     * @param   util.Date end
     * @return  com.google.util.GoogleDateRange
     */
    public static function forDates($start, $end) {
      $range= new GoogleDateRange();
      $range->setStart($start);
      $range->setEnd($end);
      return $range;
    }

    /**
     * Set Start
     *
     * @param   util.Date start
     */
    public function setStart($start) {
      $this->start= $start;
    }

    /**
     * Get Start
     *
     * @return  util.Date
     */
    public function getStart() {
      return $this->start;
    }

    /**
     * Set End
     *
     * @param   util.Date end
     */
    public function setEnd($end) {
      $this->end= $end;
    }

    /**
     * Get End
     *
     * @return  util.Date
     */
    public function getEnd() {
      return $this->end;
    }
    
    /**
     * Converts a Date object to Julian daycount.
     *
     * The Julian date is calculated by the number of days since 
     * January 1, 4713 BC.
     *
     * Note: Returns zero (0) on failure.
     *
     * @param   util.Date date
     * @return  int
     */
    public static function dateToJulian($date) {
      with ($iyear= $date->getYear(), $imonth= $date->getMonth(), $iday= $date->getDay()); {
      
        // Check for invalid dates
        if (
          $iyear == 0 || $iyear < -4714 || 
          $imonth <= 0 || $imonth > 12 || 
          $iday <= 0 || $iday > 31
        ) {
          return 0;
        }
        
        // Check for dates before SDN 1 (Nov 25, 4714 B.C.)
        if ($iyear == -4714) {
          if ($imonth < 11) return 0;
          if ($imonth == 11 && $iday < 25) return 0;
        }
        
        // Make year always a positive number
        $year= $iyear + 4800 + ($iyear < 0);
        
        // Adjust the start of the year
        if ($imonth > 2) {
          $month= $imonth - 3;
        } else {
          $month= $imonth + 9;
          $year--;
        }
        
        return (int)(
          floor((floor($year / 100) * 146097) / 4) +
          floor((($year % 100) * 1461) / 4) +
          floor(($month * 153 + 2) / 5) +
          $iday -
          32045
        );
      }
    }

    /**
     * Creates string representation of this date range
     *
     * Example:
     * <pre>
     *   daterange:2452122-2452234
     * </pre>
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        'daterange:%d-%d',
        GoogleDateRange::dateToJulian($this->start),
        GoogleDateRange::dateToJulian($this->end)
      );
    }
  }
?>
