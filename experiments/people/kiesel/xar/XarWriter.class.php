<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('class', 'cli');

  define('OPTION_SIMULATE',     0x0001);
  define('OPTION_VERBOSE',      0x0002);

  // {{{ final class XarWriter
  class XarWriter extends Object {
    var
      $command  = '',
      $options  = 0,
      $filename = '',
      $args     = array();
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function main($args) {
      $xar= &new XarWriter();
      $xar->args= &$args;
      
      try(); {
        $xar->parseParameters();
      } if (catch('IllegalArgumentException', $e)) {
        $e->printStackTrace();
        exit(1);
      }
      
      try(); {
        $retval= $xar->executeCommand();
      } if (catch('Exception', $e)) {
        $e->printStackTrace();
        exit(1);
      }
      
      return $retval;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function parseParameters() {
      $pos= 1;
      $command= $this->args->value($pos);
      
      for ($i= 0; $i < strlen($command); $i++) {
        switch ($command{$i}) {
          case 'c': $this->command= 'create'; break;
          case 'x': $this->command= 'extract'; break;
          case 't':
            $this->command= 'extract';
            $this->options|= OPTION_SIMULATE;
            break;
          case 'v': $this->options|= OPTION_VERBOSE; break;
          case 'f': $this->filename= $this->args->value($pos+ 1); break;
          default: return throw(new IllegalArgumentException('Unsupported commandline option "'.$command{$i}.'"'));
        }
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function executeCommand() {
      try(); {
        $class= &XPClass::forName('net.xp_framework.xarwriter.command.'.ucfirst($this->command).'Command');
      } if (catch('ClassNotFoundException', $e)) {
        return throw(new IllegalStateException('Unsupported or unparseable commandline: '.$this->command));
      }
      
      $instance= &$class->newInstance($this->options, $this->filename, $this->args);
      $method= &$class->getMethod('perform');
      return $method->invoke($instance);
    }
  } runnable();
  // }}}
?>
