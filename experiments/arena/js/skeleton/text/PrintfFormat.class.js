{
/* This class is part of the XP javascript framework
 *
 * $Id$
 */

  uses('String');

  /**
   * Printf Formatter
   *
   * @purpose  sprintf implementation
   */
  function PrintfFormat(fmtstring) {

    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     */      
    {
      this.fmtstring= fmtstring;
    }

    /**
     * Formats a string
     *
     * @access  public
     * @param   mixed* args
     * @return  string
     */
    PrintfFormat.prototype.format = function() {
      var i= 0, c= 0, pos= 0, padwidth= 0, padwhere= 0, p= 0;
      var output= '', padchar= '', subst= '';
      var fmtstring= this.fmtstring;  // Create a temporary copy

      while (-1 != (pos= fmtstring.indexOf('%'))) {
        output+= fmtstring.substring(0, pos);
        
        // Find the token
        offset= 1;
        while (
          (-1 == fmtstring.charAt(pos + offset).indexOf('oubdfscxX')) &&
          (offset < fmtstring.length)
        ) offset++;
        token= fmtstring.charAt(pos + offset - 1);
        params= fmtstring.substring(pos + 1, offset - 1);
        
        // Padding
        padchar= ' ';
        padwidth= 0;
        padwhere= 0;
        p= 0;
        
        // Figure out where to pad
        switch (params.charAt(0)) {
          case '+': padwhere = 0; p++; break; // left
          case '-': padwhere = 1; p++; break; // right
        }
        
        // Figure out padding character
        if (params.charAt(p) == '0') {
          padchar= '0'; 
          p++;
        }
        
        // Figure out padding width
        padwidth= parseInt(params.substring(p, params.length));
        
        // Switch on the token
        switch (token) {
          case 'f':
            subst= String(parseFloat(arguments[i]));
            break;
          
          case 's':
            subst= String(arguments[i]);
            break;
            
          case 'c':
            subst= String(arguments[i]).charAt(0);
            break;
          
          case 'u':
            subst= String(Math.abs(parseInt(arguments[i])));
            break;

          case 'b':
            subst= parseInt(arguments[i]).toString(2);
            break;

          case 'o':
            subst= parseInt(arguments[i]).toString(8);
            break;

          case 'd':
            subst= parseInt(arguments[i]).toString(10);
            break;
                    
          case 'x':
            subst= parseInt(arguments[i]).toString(16).toLowerCase();
            break;

          case 'X':
            subst= parseInt(arguments[i]).toString(16).toUpperCase();
            break;
          
          case '%':
            subst= '%';
            break;

          default:
            throw new Exception('Invalid token "' + token +'"');
        }

        output+= subst.pad(padwidth, padchar, padwhere);
        fmtstring= fmtstring.substring(pos + offset, fmtstring.length);
        
        i++;
      }

      return output + fmtstring;
    }
  }
}
