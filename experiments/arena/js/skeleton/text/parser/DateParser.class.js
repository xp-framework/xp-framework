{
/* This class is part of the XP javascript framework
 *
 * $Id$ 
 */

  uses('Exception');

  var DATE_FMT_GERMAN = /0?([0-9]+)\.0?([0-9]+)(\.0?([0-9]+))?/;

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
  function DateParser(format) {

    /**
     * Constructor
     *
     * @access  public
     * @param   regex format
     */      
    {
      this.format= format;
    }

    /**
     * Parse a string
     *
     * @access  public
     * @param   string str
     * @return  Date
     * @throws  Exception in case
     */      
    DateParser.prototype.parse= function(str) {
      var matches= [];
      var y= 0;
      var date= null;

      if (matches= str.match(this.format)) {
        with (date = new Date()) {
          setDate(parseInt(matches[1]));
          setMonth(parseInt(matches[2]) - 1);
          if (4 < matches.length) {
            y = parseInt(matches[4]);
            setYear(y < 30 ? y + 2000 : y);
          }
        }
      }
      
      if (!date) {
        throw new Exception('Could not parse date "' + str + '"');
      }
      return date;
    }
  }
}
