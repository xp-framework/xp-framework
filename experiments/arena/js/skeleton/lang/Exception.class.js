{
/* This class is part of the XP javascript framework
 *
 * $Id$ 
 */

  /**
   * Exception
   *
   * @purpose  Base class
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
