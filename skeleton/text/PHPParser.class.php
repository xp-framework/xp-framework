<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses (
    'io.File',
    'io.FileUtil'
  );

  // Modes of operation
  define('PHPPARSER_MODE_UNDEF',            0x0000);
  define('PHPPARSER_MODE_GET_FUNC_NAME',    0x0001);
  define('PHPPARSER_MODE_GET_CLASS_NAME',   0x0002);
  define('PHPPARSER_MODE_GET_USES',         0x0003);
  define('PHPPARSER_MODE_GET_REQUIRE',      0x0004);

  /**
   * Class to parse PHP files. Parsing tries to extract informations
   * about contained global functions, classes and its functions.
   *
   * @ext tokenizer
   */
  class PHPParser extends Object {
    var
      $functions= NULL,
      $classes=   NULL,
      $filename=  NULL,
      $uses=      NULL,
      $requires=  NULL,
      $log=       NULL;
    
    var
      $_utimeLastChange= 0;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string filename;
     */
    function __construct($filename) {
      $this->functions= $this->classes= $this->uses= $this->requires= array();
      
      $this->filename= $filename;
      $this->_utimeLastChange= 0;
    }
    
    /**
     * Sets a trace
     *
     * @access  public
     * @param   &util.log.LogCategory
     */
    function setTrace(&$log) {
      $this->log= &$log;
    }    
    
    /**
     * Returns whether this file needs reparsing
     *
     * @access  public
     * @return  boolean needreparseing
     */
    function needsReparsing() {
      if (NULL === $this->filename) return FALSE;
      
      if (FALSE !== ($mtime= filemtime ($this->filename)))
        return ($mtime > $this->getLastChange());
      
      return TRUE;
    }
    
    /**
     * Get time of last parsing
     *
     * @access  public
     * @return  int utime
     */
    function getLastChange() {
      return $this->_utimeLastChange;
    }
    
    /**
     * Clears for reparsing
     *
     * @access  public
     */
    function clear() {
      $this->functions= $this->classes= array();
      $this->_utimeLastChange= 0;
    }     

    /**
     * Parses the file.
     *
     * @access  public
     * @return  bool success
     */
    function parse() {
      try(); {
        $php= FileUtil::getContents (new File ($this->filename));
      } if (catch ('Exception', $e)) {
        return throw ($e);
      }
      
      $tokens= token_get_all ($php);
      $lineno= 1;

      // Clean up old data
      $this->clear();
      
      $currentSpace= array();
      $currentClass= array();

      $mode= PHPPARSER_MODE_UNDEF;
      foreach ($tokens as $token) {
        if (is_string ($token)) {

          // No active 'workspace' => continue
          if (0 == count ($currentSpace)) continue;

          $s= &$currentSpace[count($currentSpace)-1];
          
          switch ($token) {
            case '{':
              $s->braces++;
              break;
              
            case '}':
              $s->braces--;
              break;
            
            default:
              continue (2);
          }
          
          // If $s->braces is 0, pop the item off the stack
          if (0 == $s->braces) {
            $this->log && $this->log->debug ($s->type, $s->name, 'ends at line', $lineno);
            
            unset ($s->braces);
            $s->endsAt= $lineno;
            
            // Remove things from the stack
            if ('class' == $s->type) array_pop ($currentClass);
            array_pop ($currentSpace);
          }
          
          continue;
        }

        list ($tokenId, $data)= $token;
        switch ($mode) {
          case PHPPARSER_MODE_UNDEF:
            switch ($tokenId) {
              case T_CLOSE_TAG:
              case T_OPEN_TAG:
              case T_START_HEREDOC:
              case T_END_HEREDOC:
              case T_WHITESPACE:
              case T_ENCAPSED_AND_WHITESPACE:
              case T_INLINE_HTML:
              case T_ML_COMMENT:
              case T_NUM_STRING:
              case T_STRING:
              case T_COMMENT:
                $lineno+= substr_count ($data, "\n");
                break;

              case T_FUNCTION:
                $mode= PHPPARSER_MODE_GET_FUNC_NAME;
                break;

              case T_CLASS:
                $mode= PHPPARSER_MODE_GET_CLASS_NAME;
                break;
              
              default:
                break;
            }
            break;

          case PHPPARSER_MODE_GET_FUNC_NAME:
            switch ($tokenId) {
              case T_WHITESPACE:
                break;

              case T_STRING:
                $this->log && $this->log->info ('Function', $data, 'at line', $lineno);
                
                // Add new function, if currectClass exists add to that, otherwise to file
                $f= &new StdClass();
                $f->type= 'function';
                $f->name= $data;
                $f->line= $lineno;
                $f->braces= 0;
                
                if (0 < count ($currentClass)) {
                  $c= &$currentClass[count($currentClass)-1];
                  $c->functions[]= &$f;
                } else {
                  $this->functions[]= &$f;
                }
                
                $currentSpace[]= &$f;
                
                $mode= PHPPARSER_MODE_UNDEF;
                break;
            }
            break;

          case PHPPARSER_MODE_GET_CLASS_NAME:
            switch ($tokenId) {
              case T_WHITESPACE:
                break;

              case T_STRING:
                $this->log && $this->log->info ('Class', $data, 'at line', $lineno);
                
                // Add new class object, and remember this as the active
                $c= &new StdClass();
                $c->type= 'class';
                $c->name= $data;
                $c->line= $lineno;
                $c->braces= 0;
                $c->functions= array();
                
                $this->classes[]= &$c;
                $currentClass[]= &$c;
                $currentSpace[]= &$c;
                
                $mode= PHPPARSER_MODE_UNDEF;
                break;
            }
            break;
        }
      }
      
      // 2nd pass: try to find uses(), require() and include()
      $brace= 0; $mode= PHPPARSER_MODE_UNDEF;
      foreach ($tokens as $token) {
        if (is_string($token)) {
          switch ($token) {
            case '(':
              $brace++;
              break;
            case ')':
              $brace--;
              break;
            
            default:
              break;
          }

          if ($brace == 0) $mode= PHPPARSER_MODE_UNDEF;
          continue;
        }
        
        list ($tokenId, $data)= $token;
        switch ($tokenId) {
          case T_STRING:
            if ('uses' == $data) $mode= PHPPARSER_MODE_GET_USES;
            break;
          
          case T_INCLUDE:
          case T_INCLUDE_ONCE:
          case T_REQUIRE:
          case T_REQUIRE_ONCE:
            $mode= PHPPARSER_MODE_GET_REQUIRE;
            break;
        }
        
        switch ($mode) {
          case PHPPARSER_MODE_GET_USES:
            if (T_CONSTANT_ENCAPSED_STRING == $tokenId) 
              $this->uses[]= substr ($data, 1, strlen ($data)-2);
            break;
          
          case PHPPARSER_MODE_GET_REQUIRE:
            if (T_CONSTANT_ENCAPSED_STRING == $tokenId) 
              $this->requires[]= substr ($data, 1, strlen ($data)-2);
          
          default:
            break;
        }
      }
      
      $this->_utimeLastChange= time();
      return TRUE;
    }
  }

?>
