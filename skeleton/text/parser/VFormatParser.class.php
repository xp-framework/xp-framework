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
   *   $p->setHandler('EMAIL', array(&$this, 'setEmail'));
   *   $p->setHandler('NICKNAME', array(&$this, 'setNick'));
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
   * @see      reference
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
     * @param   &function func
     */
    function setDefaultHandler(&$func) {
      $this->handlers[NULL]= &$func;
    }
    
    /**
     * Set handler for an element
     *
     * @access  public
     * @param   string element
     * @param   &function func
     */
    function setHandler($element, &$func) {
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
          'Expecting "END:'.$this->identifier.'", have "'.$l.'"'
        ));
      }
      
      do {
        if ($this->_parse($l= $stream->readLine())) continue;
        $result= FALSE;
        break;
      } while (!$this->_checkFooter($l) && !$stream->eof());
      $stream->close();
      
      return TRUE;
    }

    /**
     * Parse a single line
     *
     * @access  private
     * @param   string line
     * @return  bool success
     */
    function _parse($s) {
      static $line= '';
      
      // Discard empty lines
      if (empty($s)) return TRUE;
      
      $line.= $s;
      list($key, $value)= explode(':', $line, 2);

      // Divided values
      if (FALSE !== ($i= strpos($key, ';'))) {
        $kargs= explode(';', strtoupper($key));
        $key= substr($key, 0, $i);
      } else {
        $kargs= array(strtoupper($key));
      }

      // Charsets and encodings
      for ($i= 0, $m= sizeof($kargs); $i< $m; $i++) switch ($kargs[$i]) {
        case 'CHARSET=UTF-8': 
          $value= utf8_decode($value); 
          break;

        case 'QUOTED-PRINTABLE':
          if ('=' == $value{strlen($value)- 1}) {
            $line= substr($line, 0, -1);
            return TRUE;
          }
          $value= quoted_printable_decode($value);
          break;

      }
      
      // Call handler
      $func= (isset($this->handler[$kargs[0]]) 
        ? $this->handler[$kargs[0]] 
        : $this->handler[0]
      );
      if (!call_user_func($func, $kargs, explode(';', $value))) {
        $fname= is_array($func) ? get_class($func[0]).'::'.$func[1] : $func;
        return throw(new MethodNotImplementedException('Could not invoke callback for '.$fname));
      }

      $line= '';
      return TRUE;
    }
  
    /**
     * Check for a valid header
     *
     * @access  private
     * @param   string hdr Line where header is supposedly located
     * @return  bool valid
     */
    function _checkHeader($hdr) {
      return (strcmp(
        'BEGIN:'.$this->identifier,
        substr($hdr, 0, strlen($this->identifier)+ 6)
      ) == 0);
    }

    /**
     * Check for a valid footer
     *
     * @access  private
     * @param   string hdr Line where footer is supposedly located
     * @return  bool valid
     */
    function _checkFooter($hdr) {
      return (strcmp(
        'END:'.$this->identifier,
        substr($hdr, 0, strlen($this->identifier)+ 4)
      ) == 0);
    }

  }
?>
