<?php
/* This class is part of the XP framework
 *
 * $Id: MessageFormat.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace text::format;

  uses(
    'text.format.IFormat',
    'text.format.PrintfFormat',
    'text.format.DateFormat',
    'text.format.ChoiceFormat',
    'text.format.NumberFormat',
    'text.format.ArrayFormat',
    'text.format.HashFormat'
  );
  
  /**
   * Message formatter
   *
   * 
   * <code>
   *   $mf= &new MessageFormat(
   *     '{2,date,%Y-%m-%d} The disk "{1}" contains {0,printf,%4d} file(s)'
   *   );
   *   $message= array();
   *   try(); {
   *     $message[]= $mf->format(1282, 'MyDisk', new Date(time()));
   *     $message[]= $mf->format(42, 'HomeDisk', new Date(time()));
   *   } if (catch('FormatException', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *   
   *   var_dump($message);
   * </code>
   *
   * <code>
   *   $mf= &new MessageFormat(
   *     'The disk "{1}" contains {0,choice,0:no files|1:one file|*:{0,number,0#,#`} files}.'
   *   );  
   *   $message= array();
   *   try(); {
   *     $message[]= $mf->format(1282, 'MyDisk');
   *     $message[]= $mf->format(1, 'MyDisk');
   *     $message[]= $mf->format(0, 'MyDisk');
   *   } if (catch('FormatException', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *   
   *   var_dump($message);
   * </code>
   *
   * @purpose  Format strings
   */
  class MessageFormat extends IFormat {
    public
      $formatters   = array();
      
    /**
     * Constructor
     *
     * @param   string f default NULL format string
     */
    public function __construct($f= ) {

      // Add some default formatters
      $this->setFormatter('printf', PrintfFormat::getInstance());
      $this->setFormatter('date',   DateFormat::getInstance());
      $this->setFormatter('choice', ChoiceFormat::getInstance());
      $this->setFormatter('number', NumberFormat::getInstance());
      $this->setFormatter('array',  ArrayFormat::getInstance());
      $this->setFormatter('hash',   HashFormat::getInstance());

      parent::__construct($f);
    }
    
    /**
     * Get an instance
     *
     * @return  text.format.MessageFormat
     */
    public function getInstance() {
      return parent::getInstance('MessageFormat');
    }
  
    /**
     * Set a format handler for a special type
     *
     * @param   string alias
     * @param   text.format.PrintfFormat formatter
     * @return  text.format.PrintfFormat formatter
     * @throws  lang.IllegalArgumentException 
     */
    public function setFormatter($alias, $formatter) {
      if (!is('IFormat', $formatter)) {
        throw(new lang::IllegalArgumentException('Formatter must be a text.format.Format'));
      }
      $this->formatters[$alias]= $formatter;
      return $this->formatters[$alias];
    }
    
    /**
     * Check whether a given formatter exists
     *
     * @param   string alias
     * @return  bool true in case the specified formatter exists, false otherwise
     */
    public function hasFormatter($alias) {
      return isset($this->formatters[$alias]);
    }
    
    /**
     * Apply format to argument
     *
     * @param   mixed fmt
     * @param   mixed argument
     * @return  string
     */
    public function apply($fmt, $argument) {
      static $instance;
      static $level= 0;
      
      if (FALSE === ($p= strpos($fmt, '{'))) return $fmt;
      if (!isset($instance)) {
        $instance= ::getInstance();
      }
      if (!is_array($argument)) $argument= array($argument);
      $level++;
      
      // Loop while {'s can be found
      $result= '';
      do {
        $result.= substr($fmt, 0, $p);

        // Find corresponding closing bracket
        $index= $rest= FALSE;
        $c= 0;
        for ($i= $p, $l= strlen($fmt); $i < $l; $i++) {
          switch ($fmt{$i}) {
            case '{': $c++; break;
            case '}': 
              if (0 >= --$c) {
                $index= substr($fmt, $p+ 1, $i- $p- 1);
                $fmt= substr($fmt, $i+ 1);
                break 2; 
              }
              break;
          }
        }
        
        // No closing bracket found
        if (FALSE === $index) {
          trigger_error(sprintf(
            'Opening bracket found at position %d of "%s"',
            $p,
            $fmt
          ), E_USER_NOTICE);
          throw(new lang::FormatException('Parse error [level '.$level.']: closing curly bracket not found'));
        }
        
        // Syntax: {2} = paste argument, {2,printf,%s} use formatter
        if (FALSE !== strpos($index, ',')) {
          list($index, $type, $param)= explode(',', $index, 3);
        } else {
          $type= $param= NULL;
        }
        
        // Check argument index
        if (!isset($argument[$index])) {
          throw(new lang::FormatException('Missing argument at index '.$index));
        }
        
        // Default
        if (NULL == $type) {
          $result.= (is_object($argument[$index]) && method_exists($argument[$index], 'toString')
            ? $argument[$index]->toString()
            : $argument[$index]
          );
          continue;
        }
        
        // No formatter registered
        if (!$this->hasFormatter($type)) {
          throw(new lang::FormatException('Unknown formatter "'.$type.'"'));
        }
        
        // Formatters return FALSE to indicate failure
        if (FALSE === ($format= $this->formatters[$type]->apply($param, $argument[$index]))) {
          return FALSE;
        }
        
        // Look to see if a formatstring was returned
        if (FALSE !== strpos($format, '{')) {
          $format= $instance->apply($format, $argument[$index]);
        }
        
        // Append formatter's result
        $result.= $format;
      } while (FALSE !== ($p= strpos($fmt, '{')));
      
      return $result.$fmt;    
    }
  }
?>
