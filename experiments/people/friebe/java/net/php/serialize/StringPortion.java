/* This class is part of the XP framework's Java extension
 *
 * $Id$ 
 */

  package net.php.serialize;

  /**
   *
   */
  private class StringPortion {
    private String string;
    private int end;

    /**
     * Constructor
     *
     * @access  public
     * @param   String s
     * @param   int begin
     * @param   int length
     */    
    public StringPortion(String s, int begin, int length) {
      this.end= begin + length;
      this.string= s.substring(begin, end);
    }
    
    /**
     * Retrieves string
     *
     * @access  public
     * @return  String
     */
    public String getString() {
      return string;
    }
    
    /**
     * Retrieves ending offset
     *
     * @access  public
     * @return  String
     */
    public int getEnd() {
      return end;
    }
  }
