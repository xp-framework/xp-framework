{
/* This adaptor is part of the XP javascript framework
 *
 * $Id$
 */

  uses('Exception');

  /**
   * String adaptor
   *
   * @purpose  Add functionality to the built-in String class
   */
  {
  
    /**
     * Formats a string
     *
     * @access  public
     * @param   string str the format string
     * @param   mixed* args
     * @return  string
     */
    String.prototype.format = function() {
      var ret= '';
      var str= String(this);
      var i= 0;

      while (-1 != (pos= str.indexOf('%'))) {
        ret+= str.substring(0, pos);
        i++;
        token= str.charAt(pos + 1);
        
        if ('0' == str.charAt(pos + 1)) {
          pad= '0';
        }
        
        switch (token) {
          case 'd': 
            subst= String(parseInt(arguments[i]));
            break;

          case 'f':
            subst= String(parseFloat(arguments[i]));
            break;
          
          case 's':
            subst= String(arguments[i]);
            break;
            
          case 'c':
            subst= String(arguments[i]).charAt(0);
            break;
          
          default:
            throw new Exception('Invalid token "' + token +'"');
        }
        
        for (c= 0; c < subst.length; c++) {
          subst = pad + subst;
        }
        
        str= str.substring(pos + 2, str.length);
      }

      return ret + str;
    }

  }
}
