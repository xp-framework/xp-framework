{
/* This adaptor is part of the XP javascript framework
 *
 * $Id$
 */

  uses('PrintfFormat');

  /**
   * Date adaptor
   *
   * @purpose  Add functionality to the built-in Date class
   */
  {

    /**
     * Adds years, months and/or days to a date and returns
     * a new date
     *
     * @access  public
     * @param   int years
     * @param   int months
     * @param   int days
     * @return  Date
     */      
    Date.prototype.add= function(years, months, days) {
      date= new Date(
        this.getFullYear(),
        this.getMonth(),
        this.getDate(),
        this.getHours(),
        this.getMinutes(),
        this.getSeconds()
      );

      with (date) {
        if (years) setFullYear(getFullYear() + years);
        if (months) setMonth(getMonth() + months);
        if (days) setDate(getDate() + days);
      }
      return date;
    }

    /**
     * Format a date
     *
     * @access  public
     * @param   string str the format string
     * @return  string the formatted date
     * @see     php://strftime
     */      
    Date.prototype.format= function(str) {
      var ret= '';
      var lzero= new PrintfFormat('%02d');
      var pos= 0;
      
      while (-1 != (pos= str.indexOf('%'))) {
        ret+= str.substring(0, pos);
        switch (str.charAt(pos + 1)) {
          case 'd': ret+= lzero.format(this.getDate()); break;
          case 'm': ret+= lzero.format(this.getMonth() + 1); break;
          case 'Y': ret+= this.getFullYear(); break;
          // TBI: Rest
        }
        str= str.substring(pos + 2, str.length);
      }
      return ret + str;
    }
  }
}
