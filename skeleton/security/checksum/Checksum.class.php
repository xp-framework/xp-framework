<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Checksum
   *
   * Usage [Getting an MD5 checksum from a string]
   * <code>
   *   $md5= &MD5::fromString('Hello world');
   *   var_dump($md5->getValue());
   * </code>
   *
   * Usage [Getting an SHA1 checksum from a file]
   * <code>
   *   $sha1= &SHA1::fromFile(new File('dummy'));
   *   var_dump($sha1->getValue());
   * </code>
   *
   * Usage [Verifying a CRC32 against a file]
   * <code>
   *   $crc32= &new CRC32(1140816021);
   *   if (!$crc32->verify(CRC32::fromFile(new File('verify.me')))) {
   *     echo 'Verify failed';
   *   } else {
   *     echo 'Verify OK';
   *   }
   * </code>
   *
   * @purpose  Abstract base class to all other checksums
   */
  class Checksum extends Object {
    var
      $value = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   mixed value
     */
    function __construct($value) {
      $this->value= $value;
      parent::__construct();
    }
  
    /**
     * Create a new checksum from a string. Override this
     * method in child classes!
     *
     * @access  abstract
     * @param   string str
     */
    function &fromString($str) { }

    /**
     * Create a new checksum from a file object. Override this
     * method in child classes!
     *
     * @access  abstract
     * @param   &io.File file
     */
    function &fromFile(&$file) { }
    
    /**
     * Retrieve the checksum's value
     *
     * @access  public
     * @return  mixed value
     */
    function getValue() {
      return $this->value;
    }
  
    /**
     * Verify this checksum against another checksum
     *
     * @access  public
     * @param   &security.checksum.Checksum
     * @return  bool TRUE if these checksums match
     */
    function verify(&$sum) {
      return $this->value === $sum->value;
    }
  }
?>
