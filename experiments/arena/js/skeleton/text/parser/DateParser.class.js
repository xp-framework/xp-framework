{
/* This class is part of the XP javascript framework
 *
 * $Id$ 
 */

  uses('Exception');

  /**
   * Dateparser
   *
   * Supported schemes:
   * - DD.MM.YYYY
   * - DD.MM.YY 
   * - DD.MM
   *
   * @purpose  Parse dates
   */
  function DateParser(str) {

    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     * @throws  Exception in case
     */      
    {
      if (matches= str.match(/0?([0-9]+)\.0?([0-9]+)(\.0?([0-9]+))?/)) {
        with (this.date = new Date()) {
          setDate(parseInt(matches[1]));
          setMonth(parseInt(matches[2]));
          if (4 < matches.length) {
            y = parseInt(matches[4]);
            setYear(y < 2000 ? y + 2000 : y);
          }
        }
      }
      
      if (!this.date) {
        throw new Exception('Could not parse date "' + str + '"');
      }
    }

    /**
     * Retrieve parsed date
     *
     * @access  public
     * @return  Date
     */      
    DateParser.prototype.getDate= function() {
      return this.date;
    }
  }
}
