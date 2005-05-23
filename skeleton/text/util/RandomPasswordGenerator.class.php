<?php
/* This class is part of the XP framework
 *
 * $Id: RandomCodeGenerator.class.php,v 1.2 2004/06/03 22:16:19 friebe Exp $ 
 */

  uses('text.StringUtil');

  /**
   * Generates random password
   *
   * TODO: allow generation of customer-friendly passwords (no S/5, 1/l, ...)
   *
   * @purpose   Generator
   */
  class RandomPasswordGenerator extends Object {

    var
      $lc= 'abcdefghijklmnopqrstuvwxyz',
      $uc= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
      // TODO: split special characters into printable/non-printable?
      // are all special characters allowed in database?
      // $sc= '_-=+!?#@$%^&*()[]|\/{}"\':;.><,',
      $sc= '_-=+!?#@$%^&*()[]{}/\:;.,<>', // don't allow single or double quotes
      $nc= '0123456789',
      $chars= '',
      $length= 8;

    /**
     * Constructor
     * type can contain the followin characters:
     *  'c' to include capitals
     *  'n' to include numbers
     *  's' to include special characters
     *
     * @access  public
     * @param   int length default 8
     * @param   string type default NULL
     */
    function __construct($length= 8, $type= NULL) {
      $this->length= $length;
      if (NULL === $type) {
        $this->chars= $this->lc.$this->uc.$this->sc.$this->nc;
      } else {
        $this->chars= $this->lc;
        if (FALSE !== strpos($type, 'c')) {
          $this->chars.= $this->uc;
        }
        if (FALSE !== strpos($type, 'n')) {
          $this->chars.= $this->nc;
        }
        if (FALSE !== strpos($type, 's')) {
          $this->chars.= $this->sc;
        }
      }
    }

    /**
     * Generate
     *
     * @access  public
     * @return  string
     */
    function generate() {
      $pass= "";
      for ($i= 0; $i< $this->length; $i++) {
        // $temp= str_shuffle($this->chars);
        // $pass.= $temp[0];
        $pass.= $this->chars[mt_rand(0, strlen($this->chars))];
      }
      return $pass;
    }

  }
?>
