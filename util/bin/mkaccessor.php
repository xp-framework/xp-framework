<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  uses('io.sys.StdStream');
  
  // {{{ main
  $stdin= &StdStream::get(STDIN);
  $str= '';
  while (!$stdin->eof()) {
    $str.= $stdin->read();
  }

  $t= strtok($str, "\r\n");
  do {
    list($var, $init)= explode('=', $t, 2);
    $var= trim($var);
    $init= substr(trim($init), 0, -1);
    
    switch (strtolower($init)) {
      case 'null':  // Object
        $type= 'lang.Object';
        $ref= TRUE;
        break;
        
      case "''":    // String
        $type= 'string';
        $ref= FALSE;
        break;

      case '0':     // Int
        $type= 'int';
        $ref= FALSE;
        break;

      case '0.0':   // Float
        $type= 'float';
        $ref= FALSE;
        break;

      case 'false':  // Boolean
      case 'true':
        $type= 'bool';
        $ref= FALSE;
        break;

      case 'array()':  // Array
        $type= 'mixed[]';
        $ref= FALSE;
        break;
        
      default:
        $type= 'mixed';
        $ref= FALSE;
        break;
    }
    
    printf(<<<__
    /**
     * Set %1\$s
     *
     * @access  public
     * @param   %4\$s%3\$s
     */
    function set%1\$s(\$%4\$s%2\$s) {
      \$this->%2\$s= \$%4\$s%2\$s;
    }

    /**
     * Get %1\$s
     *
     * @access  public
     * @return  %4\$s%3\$s
     */
    function %4\$sget%1\$s() {
      return \$this->%2\$s;
    }


__
    , ucfirst(substr($var, 1)), substr($var, 1), $type, $ref ? '&' : '');    
  } while ($t= strtok("\r\n"));
  
  // }}}
?>
