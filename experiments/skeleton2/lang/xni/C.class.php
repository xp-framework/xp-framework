<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'text.PHPTokenizer',
    'io.FileUtil',
    'io.File'
  );

  /**
   * Allow to write PHP-API like C source code into classes
   *
   * Usage [declare class]:
   * <code>
   *   uses('lang.xni.C');
   *
   *   class CSystem extends Object {
   *
   *     cfunction __inline_sysexit($exitcode) { <<<__
   *       long exitcode;
   * 
   *       if (zend_parse_parameters(ZEND_NUM_ARGS()  TSRMLS_CC, "l", &exitcode) == FAILURE) {
   *         WRONG_PARAM_COUNT;
   *       }
   *       EG(exit_status) = exitcode;
   *       zend_bailout();
   *   __;
   *     }
   *
   *   } C::__class(__FILE__);     
   * </code>
   *
   * Usage [test script]:
   * <code>
   *   uses('location.of.CSystem');
   *
   *   // Just like calling any other PHP code
   *   CSystem::sysexit(255);
   * </code>
   *
   * @purpose  Provide inline C functionality
   * @experimental
   */
  class C extends Object {
  
    /**
     * Declare class as "Class with inline C"
     *
     * @access  public
     * @param   string f filename
     * @param   string[] includes default array
     * @param   quiet default FALSE
     * @return  bool success
     */
    public function __class($f, $includes= array(), $quiet= FALSE) {
      $c= basename($f);
      $c= substr($c, 0, strpos($c, '.'));
      $module= strtolower($c);

      // Check if module exists in extension_dir
      $lib= dirname(__FILE__).'/lib/';
      if (file_exists($lib.$module.'.so')) {
        $cd= getcwd();
        chdir($lib);
        $ret= dl($module.'.so') ? $module() : FALSE;
        chdir($cd);
        return $ret;
      }
      
      // Parse class file
      try {
        $t= new PHPTokenizer();
        $t->setTokenString(FileUtil::getContents(new File($f)));
      } catch (XPException $e) {
        $quiet || $e->printStackTrace();
        return FALSE;
      }

      $inline= FALSE;
      $src= array();
      if ($tok= $t->getFirstToken()) do {
         switch ($tok[0]) {
          case T_FUNCTION:
            $f= 'cfunction' == $tok[1];
            break;

          case T_STRING:
            if ($f) $cf= strtolower(substr($tok[1], 9));
            $f= FALSE;
            break;

          case T_START_HEREDOC:
            if ('<<<__' == substr($tok[1], 0, 5)) {
              $src[$cf]= '';
              $inline= TRUE;
              continue 2;
            }
            break;

          case T_END_HEREDOC:
            if ('__' == substr($tok[1], 0, 2)) {
              $inline= FALSE;
              $src[$cf]= chop($src[$cf]);
            }
            break;
        }
        if (!$inline) continue;

        // Have inline function
        $src[$cf].= $tok[1];

      } while ($tok= $t->getNextToken());

      // Build source, function entries and forward declarations
      $fs= $fe= $fd= '';
      foreach ($src as $func => $s) {
        $fe.= sprintf("  PHP_FE(%s, NULL)\n", $func);
        $fd.= sprintf("PHP_FUNCTION(%s);\n", $func);
        $fs.= sprintf(
          "PHP_FUNCTION(%s) {\n%s\n}\n\n", 
          $func, 
          str_replace("\n    ", "\n", substr($s, 4))
        );
      }
      
      // Includes
      if (!empty($includes)) array_unshift($includes, NULL);

      // Write .c sourcecode
      $out= new File($module.'.c');
      try {
        $out->open(FILE_MODE_WRITE);
        $out->write(sprintf(<<<__
#ifdef HAVE_CONFIG_H
#include "config.h"
#endif
#include "php.h"
#include "php_ini.h"

%7\$s

PHP_FUNCTION(%1\$s);

%3\$s
function_entry %1\$s_functions[] = {
  PHP_FE(%1\$s, NULL)
  {NULL, NULL, NULL}
};

function_entry %1\$s_class_functions[] = {
%4\$s  {NULL, NULL, NULL}
};

zend_module_entry %1\$s_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
  STANDARD_MODULE_HEADER,
#endif
  "%1\$s",
  %1\$s_functions,
  NULL,
  NULL,
  NULL,
  NULL,
  NULL,
#if ZEND_MODULE_API_NO >= 20010901
  NO_VERSION_YET,
#endif
  STANDARD_MODULE_PROPERTIES
};

ZEND_GET_MODULE(%1\$s)

PHP_FUNCTION(%1\$s) {
  zend_class_entry *ce= NULL;
  zend_function public function function;
  zend_internal_function *internal_function = (zend_internal_function *)&function;
  int i;

  if (FAILURE == zend_hash_find(
    EG(class_table), 
    "%1\$s", 
    sizeof("%1\$s"), 
    (void **)&ce)
  ) {
    zend_error(E_CORE_ERROR, "Cannot find %2\$s in classtable");
    RETURN_FALSE;
  }

  for (i= 0; i < %6\$d; i++) {
    internal_function->handler= %1\$s_class_functions[i].handler;
    internal_function->arg_types= %1\$s_class_functions[i].func_arg_types;
    internal_function->function_name= %1\$s_class_functions[i].fname;
    internal_function->type= ZEND_INTERNAL_FUNCTION;

    if (FAILURE == zend_hash_add(
      &ce->function_table, 
      %1\$s_class_functions[i].fname, 
      strlen(%1\$s_class_functions[i].fname)+ 1, 
      &public function function, 
      sizeof(zend_function), 
      NULL
    )) {
      zend_error(E_CORE_ERROR, "Cannot register %2\$s::%%s", %1\$s_class_functions[i].fname);
      RETURN_FALSE;
    }
  }
  RETURN_TRUE;
}

%5\$s
__
, $module, $c, $fd, $fe, chop($fs), sizeof($src), implode("\n#include", $includes)));
        $out->close();
      } catch (XPException $e) {
        $quiet || $e->printStackTrace();
        return FALSE;      
      }
      
      // TBD: make
      
      return TRUE;
    }
  }
?>
