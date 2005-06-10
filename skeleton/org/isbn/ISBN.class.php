<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * An ISBN is a number that identifies a book for purposes of commerce 
   * and supply chains. The last digit of the ISBN is a check digit used 
   * to detect transcription errors.
   *
   * Note: This class supports both 10-digit and 13-digit numbers
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
      switch ($s= strlen($stripped= str_replace('-', '', $isbn))) {
        case 10: {      // ISBN10, until 01.01.2007
          for ($product= 0, $i= 0; $i < 9; $i++) {

            // If we encounter an invalid character, break immediately
            if (!is_numeric($stripped{$i})) return FALSE;

            $product+= $stripped{$i} * (10 - $i);
          }
          return (11 - $product % 11) % 11 == ($stripped{9} == 'X' ? 10 : $stripped{9});
        }

        case 13: {     // ISBN13, transitional from 01.01.2005 - 01.01.2007
          for ($product= 0, $i= 0; $i < 12; $i++) {
          
            // If we encounter an invalid character, break immediately
            if (!is_numeric($stripped{$i})) return FALSE;
            $product+= $stripped{$i} * (1 + ($i % 2 ? 2 : 0));
          }
          return (10 - $product % 10) % 10 == $stripped{12};
        }
      }

      return FALSE;
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
