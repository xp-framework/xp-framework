<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * An ISBN is a 10-digit number that identifies a book for purposes of 
   * commerce and supply chains. The last digit of the ISBN is a check 
   * digit used to detect transcription errors.
   *
   * @see      http://www.isbn.org/
   * @see      http://www.isbn.org/standards/home/isbn/us/isbnqa.asp - ISBN FAQ 
   * @see      http://isbntools.com/
   * @see      http://isbntools.com/details.html - Format details
   * @purpose  Represent an International Standard Book Number (ISBN)
   */
  class ISBN extends Object {
    var
      $number = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string isbn
     * @throws  lang.IllegalArgumentException in case the ISBN is invalid
     */
    function __construct($isbn) {
      if (!ISBN::isValid($isbn)) {
        return throw(new IllegalArgumentException('ISBN "'.$isbn.'" is invalid'));
      }
      $this->number= $isbn;
    }

    /**
     * Get Number
     *
     * @access  public
     * @return  string
     */
    function getNumber() {
      return $this->number;
    }
    
    /**
     * Verify an ISBN number
     *
     * @model   static
     * @access  public
     * @param   string isbn
     * @return  bool TRUE if the number is valid
     */
    function isValid($isbn) {
      static $values= '0123456789X';

      for ($checksum= 0, $weight= 10, $i= 0, $s= strlen($isbn); $i < $s; $i++) {
        if ('-' == $isbn{$i}) continue;

        // If we encounter an invalid character, break immediately
        if (FALSE === ($value= strpos($values, $isbn{$i}))) return FALSE;
        
        // If we encounter an X in an invalid place (may only be at the 
        // end), break immediately
        if ($value == 10 && $weight != 1) return FALSE;

        $checksum+= $weight * $value;
        $weight--;
      }

      return 0 === ($checksum % 11);
    }
    
    /**
     * Returns a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'('.$this->number.')';
    }
  }
?>
