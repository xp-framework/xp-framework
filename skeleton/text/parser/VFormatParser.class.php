<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
  class VFormatParser extends Object {
    var
      $identifier   = '',
      $handlers     = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string identifier, e.g. "VCARD"
     */
    function __construct($identifier) {
      $this->identifier= $identifier;
      parent::__construct();
    }
    
    /**
     * Set default handler
     *
     * @access  public
     * @param   function func
     */
    function setDefaultHandler($func) {
      $this->handlers[NULL]= &$func;
    }
    
    /**
     * Set handler for an element
     *
     * @access  public
     * @param   string element
     * @param   function func
     */
    function setHandler($element, $func) {
      $this->handlers[$element]= &$func;
    }
    
    /**
     * Parse a stream
     *
     * @access  public 
     * @param   &io.Stream stream
     * @return  bool success
     * @throws  FormatException
     */
    function parse(&$stream) {
      $stream->open(STREAM_MODE_READ);
      if (!($result= $this->_checkHeader($l= $stream->readLine()))) {
        $stream->close();
        return throw(new FormatException(
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
    
    /**
     * Parse a key->value pair
     *
     * @access  private
     * @param   string key
     * @param   string value
     * @return  bool success
     */
    function _parse($key, $value) {
      // DEBUG printf(">>> %s::=%s\n", $key, $value); 
      
      // Property params
      if (FALSE !== ($i= strpos($key, ';'))) {
        $kargs= explode(';', strtolower($key));
      } else {
        $kargs= array(strtolower($key));
      }
      
      // Charsets and encodings
      for ($i= 0, $m= sizeof($kargs); $i < $m; $i++) switch ($kargs[$i]) {
        case 'charset=utf-8': 
          $value= utf8_decode($value); 
          break;
          
        case 'encoding=base64':
          $value= base64_decode($value); 
          break;

        case 'encoding=quoted-printable':
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
      
      // DEBUG echo "-----------------------------------------------------------------------------------\n";
      if (FALSE === call_user_func($func, $kargs, explode(';', $value))) {
        trigger_error('Callback:'.(is_array($func) ? get_class($func[0]).'::'.$func[1] : $func), E_USER_NOTICE);
        return throw(new MethodNotImplementedException('Could not invoke callback for "'.$kargs[0].'"'));
      }
      
      return TRUE;
    }
  
    /**
     * Check for a valid header
     *
     * @access  private
     * @param   string l Line where header is supposedly located
     * @return  bool valid
     */
    function _checkHeader($l) {
      return (strcasecmp(
        'BEGIN:'.$this->identifier,
        substr($l, 0, strlen($this->identifier)+ 6)
      ) == 0);
    }

    /**
     * Check for a valid footer
     *
     * @access  private
     * @param   string l Line where footer is supposedly located
     * @return  bool valid
     */
    function _checkFooter($l) {
      return (strcasecmp(
        'END:'.$this->identifier,
        substr($l, 0, strlen($this->identifier)+ 4)
      ) == 0);
    }

  }
?>
