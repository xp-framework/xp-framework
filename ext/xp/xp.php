<?php
  uses(
    'lang.XPClass',
    'lang.Exception',
    'lang.IllegalAccessException',
    'lang.IllegalArgumentException',
    'lang.IllegalStateException',
    'lang.FormatException'
  );

  uses('io.File', 'io.ZipFile');
  
  function error($errno, $errstr, $errfile, $errline) {
    $GLOBALS['php_errormessage'][]= $errstr;
    $GLOBALS['php_errorline'][]= $errline;
    $GLOBALS['php_errorfile'][]= $errfile;
    $GLOBALS['php_errorcode'][]= $errno;
  }

  function is_error() {
    return empty($GLOBALS['php_errormessage']) ? FALSE : $GLOBALS['php_errormessage'];
  }
  
  set_error_handler('error');
  
 
  /*
  $s= 'string';
  $n= NULL;
  var_dump(
    cast($s, 'string'),
    cast($n, 'string')
  );
  */

  /*  
  $z= new Zipfile();
  var_dump($z->getClass(), $z->toString());
  */
  /*
  class Test extends Object {
    var $lastchange = 'now';
	
	function __construct($params= NULL) {
	  parent::__construct($params);
	  $this->lastchange = 'later';
	}
	
	function foo() {
	  echo __CLASS__.'::'.__FUNCTION__.'(';
	  var_export(func_get_args());
	  echo ")\n";
	  return '<<<'.$this->lastchange.'>>>';
	}
  }
  
  $o= new Test(array('n' => 1, 'lastchange' => 'b'));
  $o->a= 'c';
  var_dump($o->foo('a', 1, 2, array('a', 'b' => 'C')));
  var_dump($o, $o->getClass(), $o->toString());
  */
  var_dump(get_declared_classes());
  $x= new Zipfile();
  var_dump(get_parent_class($x), $x->getClass());
  $c= &$x->getClass();
  var_dump(
    $c,
    $c->getName(),
	$c->getMethods(),
	$c->getFields(),
	$c->hasMethod('toString')
  );
  
  /*
  class Out extends Object {
    function println($str) {
	  printf("Out::println('%s')\n", $str);
	}
  }
  $out= new Out();
  var_dump($out->getClass());
  
  $f= new File('/etc/passwd'); // $argv[0] /tmp/orbit-root
  try(); {
  	$f->open(FILE_MODE_READ);
	$out->println('>>> '.$f->readLine());
  } if (catch('FileNotFoundException', $e)) {
  	echo $e->getStackTrace();
	var_dump($e, $e->toString());
  } if (catch('Exception', $e)) {
  	echo $e->getStackTrace();
	var_dump($e, $e->toString());
  } finally(); {
    $out->println("Closing file");
    $f->close();
	$e && exit;
  }
  
  $out->println("Done");
  */
  /*
  $f= new File('/home/thekid/rdf_urls');
  try(); {
    $f->open(FILE_MODE_READ);
    var_dump($f->readLine());
    $f->close();
    var_dump($f->getClass());
  } if (catch('FileNotFoundException', $e)) {
    echo $e->getStackTrace();
  } 
  */
?>
