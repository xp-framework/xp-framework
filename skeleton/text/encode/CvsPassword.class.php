<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Encodes/decodes CVS passwords
   *
   * @see      http://www.loria.fr/~molli/cvs/doc/cvsclient_4.html#SEC4
   * @purpose  CVS password encoder/decoder
   */
  class CvsPassword extends Object {

    /**
     * Looks up character code in translation table
     *
     * @access  public
     * @param   int o the character code
     * @return  int the translated character code
     */
    function lookup($o) {
      static $table= array(
        114, 120,  53,  35,  36, 109,  72, 108,
         70,  64,  76,  67, 116,  74,  68,  87,
        111,  52,  75, 119,  49,  34,  82,  81,
         95,  65, 112,  86, 118, 110, 122, 105,
         64,  57,  83,  43,  46, 102,  40,  89,
         38, 103,  45,  50,  42, 123,  91,  35,
        125,  55,  54,  66, 124, 126,  59,  47,
         92,  71, 115,  91,  92, 107,  94,  56,
         96, 121, 117, 104, 101, 100,  69,  73,
         99,  63,  94,  93,  39,  37,  61,  48,
         58, 113,  32,  90,  44,  98,  60,  51,
         33,  97,  62, 123, 124, 125, 126, 127
      );
      return $table[$o];
    }

    /**
     * Encode a given string
     *
     * @access  public
     * @param   string str
     * @return  string
     */
    function encode($str) {
      for ($i= 0; $i < strlen($str); $i++) {
        $o= ord($str{$i});
        if ($o >= 32 && $o <= 127) $str{$i}= chr(CvsPassword::lookup($o- 32));
      }
      return $str;
    }

    /**
     * Decode a given string
     *
     * @access  public
     * @param   string str
     * @return  string
     */
    function decode($str) {
      return CvsPassword::encode($str);
    }
  }
?>
