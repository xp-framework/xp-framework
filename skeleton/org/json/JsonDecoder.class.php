<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('text.StringTokenizer');

  define('JSON_TOKEN_LBRACE',     0x0000);
  define('JSON_TOKEN_RBRACE',     0x0001);
  define('JSON_TOKEN_LBRACKET',   0x0002);
  define('JSON_TOKEN_RBRACKET',   0x0003);
  define('JSON_TOKEN_COMMA',      0x0004);
  define('JSON_TOKEN_COLON',      0x0005);
  define('JSON_TOKEN_VALUE',      0x1000);

  /**
   * JSON decoder and encoder
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class JsonDecoder extends Object {
  
    /**
     * Encode PHP data into 
     *
     * @access  public
     * @param   mixed data
     * @return  string
     */
    function encode($data) {
      static $controlChars= array(
        '"' => '\\"', 
        '\\'  => '\\\\', 
        '/'   => '\\/', 
        "\b"  => '\\b',
        "\f"  => '\\f', 
        "\n"  => '\\n', 
        "\r"  => '\\r', 
        "\t"  => '\\t'
      );
      switch (gettype($data)) {
        case 'string': {
          return '"'.strtr($data, $controlChars).'"';
        }
        case 'integer': {
          return (string)$data;
        }
        case 'double': {
          return strval($data);
        }
        case 'boolean': {
          return ($data ? 'true' : 'false');
        }
        case 'NULL': {
          return 'null';
        }
        
        case 'array': {
          $ret= '[ ';
          foreach ($data as $value) {
            $ret.= $this->encode($value).' , ';
          }
          
          return substr($ret, 0, -2).']';
        }
        
        case 'object': {
          $ret= '{ ';
          foreach (get_object_vars($data) as $key => $value) {
            $ret.= $this->encode((string)$key).' : '.$this->encode($value).' , ';
          }
          
          return substr($ret, 0, -2).'}';
        }
        
        default: {
          return throw(new IllegalArgumentException('Cannot encode data of type '.gettype($data)));
        }
      }
    }
    
    /**
     * Decode a string into a PHP data structure
     *
     * @access  public
     * @param   string string
     * @return  mixed
     */
    function decode($string) {
      $this->stream= &new Stream();
      $this->stream->open(STREAM_MODE_READWRITE);
      $this->stream->write($string);
      $this->stream->rewind();
      
      switch ($this->_getNextToken()) {
        case JSON_TOKEN_LBRACKET: {
          return $this->_decodeArray();
        }
        
        case JSON_TOKEN_LBRACE: {
          return $this->_decodeObject();
        }
        
        case JSON_TOKEN_VALUE: {
          return $this->_getTokenValue();
        }
      }
    }
    
    /**
     * Decode an string into array structure
     *
     * @access  protected
     * @return  array
     */
    function _decodeArray() {
      $array= array();
      do {
        $token= $this->_getNextToken();
        switch ($token) {
          case JSON_TOKEN_LBRACKET: {
            $array[]= $this->_decodeArray();
            break;
          }
          case JSON_TOKEN_LBRACE: {
            $array[]= $this->_decodeObject();
            break;
          }
          case JSON_TOKEN_VALUE: {
            $array[]= $this->_getTokenValue();
            break;
          }
        }
      } while ($token != JSON_TOKEN_RBRACKET);
      return $array;
    }
    
    /**
     * Decode string into object structure
     *
     * @access  protected
     * @return  &stdclass
     */
    function _decodeObject() {
      $obj= &new StdClass();
      do {
        $token= $this->_getNextToken();
        switch ($token) {
          case JSON_TOKEN_LBRACKET: {
            $array[]= $this->_decodeArray();
            break;
          }
          case JSON_TOKEN_LBRACE: {
            $array[]= $this->_decodeObject();
            break;
          }
          case JSON_TOKEN_VALUE: {
            if (empty($key)) {
              $key= $this->_getTokenValue();
            } else {
              $obj->{$key}= $this->_getTokenValue();
              unset($key);
            }
            break;
          }
        }
      } while ($token != JSON_TOKEN_RBRACE);

      return $obj;
    }    
    
    /**
     * Fetch next token from stream
     *
     * @access  protected
     * @return  int
     */
    function _getNextToken() {
      if ($this->stream->eof()) return JSON_TOKEN_EOF;
      $this->_trim();
      
      $token= $this->stream->read(1);
      
      switch ($token) {
        case '{': return JSON_TOKEN_LBRACE;
        case '}': return JSON_TOKEN_RBRACE;
        case '[': return JSON_TOKEN_LBRACKET;
        case ']': return JSON_TOKEN_RBRACKET;
        case ',': return JSON_TOKEN_COMMA;
        case ':': return JSON_TOKEN_COLON;
        case 't': {
          $this->_tokenValue= TRUE;
          $this->stream->read(3); // eat "rue"
          return JSON_TOKEN_VALUE;
        }
        case 'f': {
          $this->_tokenValue= FALSE;
          $this->stream->read(4); // eat "alse"
          return JSON_TOKEN_VALUE;
        }
        
        case 'n': {
          $this->_tokenValue= NULL;
          $this->stream->read(3); // eat "ull"
          return JSON_TOKEN_VALUE;
        }
        
        case '"': {
          $this->_tokenValue= $this->_readString();
          return JSON_TOKEN_VALUE;
        }
        
        case '-':
        case '+':
        case '0':
        case '1':
        case '2':
        case '3':
        case '4':
        case '5':
        case '6':
        case '7':
        case '8':
        case '9':
        case '0': {
          $this->stream->seek($this->stream->tell()- 1);
          $this->_tokenValue= $this->_readNumber();
          return JSON_TOKEN_VALUE;
        }
      }
    }
    
    /**
     * Fetch token value
     *
     * @access  protected
     * @return  mixed
     */
    function _getTokenValue() {
      return $this->_tokenValue;
    }    
    
    /**
     * Trim string, that is eat up all whitespace
     * (but not from within string)
     *
     * @access  protected
     */
    function _trim() {
      $str= $this->stream->read(10);
      $this->stream->seek($this->stream->tell() - strlen($str) + (strlen($str) - strlen(ltrim($str, ' '))));
    }
    
    /**
     * Decode string from wire
     *
     * @access  protected
     * @return  string
     */
    function _readString() {
      do {
        $initpos= $this->stream->tell();
        $offset= 0;
        $str= $this->stream->read();
        $ret= '';
      
        $esc= FALSE;
        $tokenizer= &new StringTokenizer($str, '\"', TRUE);
        while ($tokenizer->hasMoreTokens()) {
          $tok= $tokenizer->nextToken();
          $offset+= strlen($tok);
          
          if ($esc) { 
            switch ($tok) {
              case '\\':
              case '"': 
              case '/': $ret.= $tok; break;
              case 't': $ret.= "\t"; break;
              case 'n': $ret.= "\n"; break;
              case 'r': $ret.= "\r"; break;
              case 'b': $ret.= "\b"; break;
              case 'u': // XXX TBI
            }
            
            $esc= FALSE; 
            continue; 
          }
          switch ($tok) {
            case '"': {
              $this->stream->seek($initpos + $offset);
              return $ret;
            }
            
            case '\\': {
              $esc= TRUE;
              break;
            }
            
            default: {
              $ret.= $tok;
              break;
            }
          }
        }
      } while (!$this->stream->eof());
    }
    
    /**
     * Decode number from wire
     *
     * @access  protected
     * @return  mixed
     */
    function _readNumber() {
      $initpos= $this->stream->tell();
      $str= $this->stream->read();
      
      $endpos= strspn($str, '-+0123456789.eE');
      $this->stream->seek($initpos + $endpos);
      $nstr= substr($str, 0, $endpos);
      
      if (FALSE !== (strpos($nstr, '.')) || strpos(strtolower($nstr), 'e')) {
        return floatval($nstr);
      }
      
      return intval($nstr);
    }
  } implements(__FILE__, 'org.json.IJsonDecoder');
?>
