{
/* This class is part of the XP javascript framework
 *
 * $Id$ 
 */

  /**
   * Dateparser
   *
   * @purpose  Parse dates
   */
  function Exception(message) {

    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     */      
    {
      this.message= message;
    }

    /**
     * Create a string representation
     *
     * @access  public
     * @return  string
     */      
    Exception.prototype.toString= function() {
      return 'Exception "' + message + '"';
    }
  }
}
