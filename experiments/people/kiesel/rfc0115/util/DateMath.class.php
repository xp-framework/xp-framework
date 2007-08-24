<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'util.TimeZone', 'util.DateInterval');

  /**
   * Date math functions
   *
   * @ext      ext/date
   * @see      reference
   * @purpose  purpose
   */
  class DateMath extends Object {
    const
      YEAR    = 'year',
      MONTH   = 'month',
      DAY     = 'day',
      HOURS   = 'hours',
      MINUTES = 'minutes',
      SECONDS = 'seconds';

    /**
     * Diff two date objects. Only full units are returned.
     *
     * @param   util.Date date1
     * @param   util.Date date2
     * @param   util.DateInterval interval
     * @return  int
     */
    public static function diff(DateInterval $interval, Date $date1, Date $date2) {
    
      // Move both dates to GMT
      $date1= create(new TimeZone('GMT'))->convertDate($date1);
      $date2= create(new TimeZone('GMT'))->convertDate($date2);
      
      switch ($interval) {
        case DateInterval::$YEAR: {
          return -($date1->getYear()- $date2->getYear());
        }
        
        case DateInterval::$MONTH: {
          return -(
            (($date1->getYear()- $date2->getYear()) * 12) +
            ($date1->getMonth()- $date2->getMonth())
          );
        }
        
        case DateInterval::$DAY: {
          return -(intval($date1->getTime() / 86400)- intval($date2->getTime() / 86400));
        }
        
        case DateInterval::$HOURS: {
          return -(intval($date1->getTime() / 3600)- intval($date2->getTime() / 3600));
        }

        case DateInterval::$MINUTES: {
          return -(intval($date1->getTime() / 60)- intval($date2->getTime() / 60));
        }

        case DateInterval::$SECONDS: {
          return -($date1->getTime()- $date2->getTime());
        }
      }
    }
  }
?>
