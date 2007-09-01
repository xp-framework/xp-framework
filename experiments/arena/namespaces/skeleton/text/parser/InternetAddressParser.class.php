<?php
/* This class is part of the XP framework
 *
 * $Id: InternetAddressParser.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace text::parser;

  uses('peer.mail.InternetAddress', 'text.StringTokenizer');

  /**
   * Parser for InternetAddresses.
   *
   * @see      xp://peer.mail.InternetAddress
   * @purpose  Parse internet addresses
   */
  class InternetAddressParser extends lang::Object {
    public
      $_str= NULL;

    /**
     * Parse string into its InternetAddresses.
     *
     * <code>
     *   $p= &new InternetAddressParser();
     *   try(); {
     *     $addr= $p->parse('"Kiesel, Alex" <alex.kiesel@example.com>, Christian Lang <christian.lang@example.com>');
     *   } if (catch('FormatException', $e)) {
     *     $e->printStackTrace();
     *   }
     *   
     *   foreach (array_keys($addr) as $idx) { Console::writeLine($addr[$idx]->toString()); }
     *
     * </code>
     *
     * @return  InternetAddress[]
     * @throws  lang.FormatException in case the string is malformed
     */
    public function parse($str) {
      $result= array();
      $st= new text::StringTokenizer($str, ',');
      
      $st->hasMoreTokens() && $tok= $st->nextToken();
      while ($tok) {
      
        // No " in this string, so this contains one address
        if (FALSE === ($pos= strpos($tok, '"'))) {
          try {
            $result[]= peer::mail::InternetAddress::fromString($tok);
          } catch (lang::FormatException $e) {
            throw($e);
          }
          
          $tok= $st->nextToken();
          continue;
        }
        
        // When having at least one double-quote, we have to make sure, the address delimiter ','
        // is not inside the quotes. If so, search the next delimiter and perform this check again.
        // Additionally, inside a quote, the quote delimiter may be quoted with \ itself. Catch
        // that case as well.
        $inquot= 0;
        for ($i= 0; $i < strlen($tok); $i++) {
          if ($tok{$i} == '"' && (!$inquot || ($i == 0 || $tok{$i-1} != '\\'))) $inquot= 1 - $inquot;
        }
        
        if ($inquot) {
          if (!$st->hasMoreTokens()) { throw(new lang::FormatException('Cannot parse string: no ending delimiter found.')); }
          $tok= $tok.','.$st->nextToken();
          continue;
        }

        try {
          $result[]= peer::mail::InternetAddress::fromString($tok);
        } catch (lang::FormatException $e) {
          throw($e);
        }
        
        // Handle next token
        $tok= $st->nextToken();
      }
      
      return $result;
    }
  }
?>
