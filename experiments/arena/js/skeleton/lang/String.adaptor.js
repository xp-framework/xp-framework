{
/* This adaptor is part of the XP javascript framework
 *
 * $Id$
 */

  /**
   * String adaptor
   *
   * @purpose  Add functionality to the built-in String class
   */
  {

    /**
     * Pads a string
     *
     * @access  public
     * @param   int width
     * @param   string char default ' '
     * @param   int where default 0 where to pad (0 = left, 1 = right, 2= both)
     * @return  string
     */
    String.prototype.pad = function() {
      var c= 0;
      var width= arguments[0];
      var character= (arguments.length > 1) ? arguments[1] : ' ';
      var where= (arguments.length > 2) ? arguments[2] : 0;
      var str= String(this);
      
      if (!width) return str;   // Border case
      
      switch (where) {
        case 0:                 // left
          for (c= 0; c < width - str.length; c++) {
            str= character + str;
          }
          break;

        case 1:                 // right
          for (c= 0; c < width - str.length; c++) {
            str+= character;
          }
          break;

        case 2:
          for (c= 0; c < width - str.length; c++) {
            if (c & 1) str+= character; else str= character + str;
          }
          break;
      }
      
      return str;
    }
  }
}
