<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
 
  // {{{ bool enum(string class)
  //     Define an enumeration
  function enum($class) {
    if (class_exists($class)) return TRUE;
    $src= FALSE;
    $line= 1;
    $filename= $class.'.enum.php';
    $file= NULL;
    $name= NULL;
    foreach (explode(PATH_SEPARATOR, ini_get('include_path')) as $dir) {
      if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
      $file= $dir.DIRECTORY_SEPARATOR.$filename;
      break;
    }
    if (NULL === $file) {
      trigger_errror('Could not find specified file '.$filename, E_USER_WARNING);
      return FALSE;
    }
    $tokens= token_get_all(file_get_contents($file));
    for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
      // DEBUG echo $file.':'.$line.' '; var_dump($tokens[$i]);
      if (T_OPEN_TAG == $tokens[$i][0]) $src= TRUE;
      if (T_CLOSE_TAG == $tokens[$i][0]) $src= FALSE;
      $line+= substr_count($tokens[$i][1], "\n");
      if (!$src) continue;

      if (T_STRING == $tokens[$i][0] && 'enum' == $tokens[$i][1]) {
        $name= $tokens[$i+ 2][1];
        $i+= 2;
        $e= '  class '.$name.' {'."\n".'    var $__values= array(';
        while ($i < $s && '{' != $tokens[$i]) {
          $line+= substr_count($tokens[$i][1], "\n");
          $i++;
        }
        $i++;
        while ($i < $s && '}' != $tokens[$i]) {
          switch ($tokens[$i][0]) {
            case T_WHITESPACE: 
            case ',':
              break;
            
            case ';':
              $i++;
              break 2;
              
            case T_STRING:
              $define= $name.'_'.$tokens[$i][1];
              if ('(' == $tokens[$i+ 1] && ')' == $tokens[$i+ 3]) {
                $value= eval('return '.$tokens[$i+ 2][1].';');
                $i+= 3;
              } else {
                $value= $tokens[$i][1];
              }
              define($define, $value);
              $e.= $define.', ';
              break;
            
            default:
              die(sprintf(
                "Parse Error: Unexpected %s in %s on line %d\n",
                is_array($tokens[$i]) ? token_name($tokens[$i][0]) : $tokens[$i],
                $file,
                $line
              ));
          }
          $line+= substr_count($tokens[$i][1], "\n");
          $i++;
        }
        $e.= ");\n";
        $e.= '    function '.$name.'() { $a= func_get_args(); call_user_func_array(array(&$this, \'__construct\'), $a); }'."\n";
        $e.= '    function values() { $v= get_class_vars(\''.$name.'\'); return $v[\'__values\']; }'."\n";
      }
      
      if (NULL === $name) continue;
      $e.= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
    }
    
    if (!eval($e."\nreturn TRUE;")) die($e);
    return TRUE;
  }
  // }}}
?>
