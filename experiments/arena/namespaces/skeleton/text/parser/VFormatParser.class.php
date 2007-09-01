<?php
/* This class is part of the XP framework
 *
 * $Id: VFormatParser.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace text::parser;

  uses('io.Stream', 'lang.MethodNotImplementedException');

  /**
   * VFormat Parser (VCalendar, VCard, ...)
   *
   * <code>
   *   $p= &new VFormatParser('VCARD');
   *   $p->setHandler('EMAIL', 'setEmail');
   *   $p->setHandler('NICKNAME', 'setNick');
   *   $p->setDefaultHandler('var_dump');
   *   try(); {
   *     $p->parse(new File('test.vcf'));
   *   } if (catch('FormatException', $e)) {
   *     
   *     // This does not seem to be a VFormat
   *     $e->printStackTrace();
   *     exit(-1);
   *   } if (catch('Exception', $e)) {
   *
   *     // Any other error
   *     $e->printStackTrace();
   *     exit(-2);
   *   }
   * </code> 
   *
   * @purpose  Parser
   */
  class VFormatParser extends lang::Object {
    public
      $identifier   = '',
      $handlers     = array();
      
    /**
     * Constructor
     *
     * @param   string identifier, e.g. "VCARD"
     */
    public function __construct($identifier) {
      $this->identifier= $identifier;
    }
    
    /**
     * Set default handler
     *
     * @param   function func
     */
    public function setDefaultHandler($func) {
      $this->handlers[NULL]= $func;
    }
    
    /**
     * Set handler for an element
     *
     * @param   string element
     * @param   function func
     */
    public function setHandler($element, $func) {
      $this->handlers[$element]= $func;
    }
    
    /**
     * Decode a string
     *
     * Example of encoded string:
     * <pre>
     * Hi\,\Nwie angekündigt wird das Meeting auf 16:00 Uhr verschobe
     *  n.\N\NViele Grüsse\NAndrea \N
     * </pre>
     *
     * @param   string str
     * @return  string
     */
    public static function decodeString($str) {
      return strtr(utf8_decode($str), array(
        '\,'    => ',',
        '\N'    => "\n",
        '\n'    => "\n"
      ));
    }
    
    /**
     * Decodes a date string
     *
     * Example of encoded string
     * <pre>
     * 20030220T101358Z
     * </pre>
     *
     * @param   string str
     * @return  int
     */
    public static function decodeDate($str) {
      $parts= sscanf($str, '%4d%2d%2dT%2d%2d%2d');
      return mktime($parts[3], $parts[4], $parts[5], $parts[1], $parts[2], $parts[0]);
    }
       
    /**
     * Parse a stream
     *
     * @param   io.Stream stream
     * @return  bool success
     * @throws  lang.FormatException
     */
    public function parse($stream) {
      $stream->open(STREAM_MODE_READ);
      if (!($result= $this->_checkHeader($l= $stream->readLine()))) {
        $stream->close();
        throw(new lang::FormatException(
          'Expecting "BEGIN:'.$this->identifier.'", have "'.$l.'"'
        ));
      }
      
      $r= TRUE;
      $key= $value= '';
      do {
        do {
          $l= $stream->readLine();

          // Check for footer
          if ($this->_checkFooter($l)) break;

          // Discard empty lines
          if (empty($l)) continue;

          // Multiline values are indented with spaces
          if (' ' == $l{0}) {
            $value.= ltrim($l);
            continue;
          }

          // Property;Property_Param*:Property_Value
          // ------------------------ vs. ------------------------
          // Property;Property_Param*:
          //    Property_Value
          //
          // Property2:Value2
          list($k, $v)= explode(':', $l, 2);

          // Found a key->value pair
          if ($key) if (FALSE === $this->_parse($key, $value)) {
            $r= FALSE;
            break 2;
          }

          // Next round
          $key= $k;
          $value= $v;

        } while (!$stream->eof());
        
        // Parse last key->value pair
        $r= $this->_parse($key, $value);
      } while (0);
      
      $stream->close();
      
      return $r;
    }
    
    protected function _parseProperties($str) {
      $arr= array();
      $key= $val= ''; $tok= strtok ($str, ";:=");
      while ($tok) {
        if (!$key) { $key= $tok; }
        else if (!$val) { 
          $val= $tok; 
          $arr[$key]= $val;
          
          $key= $val= '';
        }
        
        $tok= strtok (";:=");
      }
      
      return $arr;
    }
    
    /**
     * Parse a key->value pair
     *
     * @param   string key
     * @param   string value
     * @return  bool success
     */
    protected function _parse($key, $value) {
     
      // Property params
      if (FALSE !== ($i= strpos($key, ';'))) {
        $props= explode(';', $key);
        $kargs= array (strtoupper (array_shift ($props)));

        $val= new ::stdClass();
        $val->_value= $value;
        foreach ($this->_parseProperties (implode(';', $props)) as $pname => $pvalue) {
          $val->{$pname}= $pvalue;
        }
        
        $value= $val;
      } else {
        $kargs= array(strtoupper($key));
      }
      
      // Charsets and encodings
      for ($i= 0, $m= sizeof($kargs); $i < $m; $i++) switch ($kargs[$i]) {
        case 'CHARSET=UTF-8': 
          $value= utf8_decode($value); 
          break;
          
        case 'ENCODING=BASE64':
          $value= base64_decode($value); 
          break;

        case 'ENCODING=QUOTED-PRINTABLE':
          $value= str_replace("\n=", "\n", quoted_printable_decode($value));
          break;
      }
      
      // Call handler
      if (isset($this->handlers[$kargs[0]])) {
        $func= $this->handlers[$kargs[0]];
        array_shift($kargs);
      } else {
        $func= $this->handlers[NULL];
      }
      
      try {
        call_user_func($func, $kargs, $value);
      } catch (lang::FormatException $e) {
        throw(new lang::MethodNotImplementedException(
          'Errors during invokation of callback for "'.$kargs[0].'": '.$e->getMessage(),
          (is_array($func) ? get_class($func[0]).'::'.$func[1] : $func)
        ));
      }
      
      return TRUE;
    }
  
    /**
     * Check for a valid header
     *
     * @param   string l Line where header is supposedly located
     * @return  bool valid
     */
    protected function _checkHeader($l) {
      return (strcasecmp(
        'BEGIN:'.$this->identifier,
        substr($l, 0, strlen($this->identifier)+ 6)
      ) == 0);
    }

    /**
     * Check for a valid footer
     *
     * @param   string l Line where footer is supposedly located
     * @return  bool valid
     */
    protected function _checkFooter($l) {
      return (strcasecmp(
        'END:'.$this->identifier,
        substr($l, 0, strlen($this->identifier)+ 4)
      ) == 0);
    }

  }
?>
