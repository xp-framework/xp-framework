<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
  require('lang.base.php');
  uses('io.File', 'text.PHPTokenizer', 'util.cmd.ParamString');
  
  $p= &new ParamString();
  if (2 != $p->count) {
    printf(
      "Strips comments and reduces whitespace on PHP sourcecode\n".
      "Usage: %s <filename>\n", 
      basename($p->value(0))
    );
    exit();
  }
  
  $t= &new PHPTokenizer();
  $f= &new File($p->value(1));
  try(); {
    $f->open(FILE_MODE_READ);
    $t->setTokens(token_get_all($f->read($f->size())));
    $f->close();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  $tok= $t->getFirstToken();
  $str= '';
  do {
    printf("[%30s] '%s'\n", $t->getTokenName($tok[0]), $tok[1]);
    switch ($tok[0]) {
      case T_COMMENT: 
        // Strip comments
        break;
        
      case T_WHITESPACE:
        // Reduce whitespace
        $str.= ' ';
        break;
      
      case T_OPEN_TAG:
        // Reduce whitespace after open tag
        $str.= '<?php';
        break;
        
      default:
        $str.= $tok[1];
    }
  } while ($tok= $t->getNextToken());
  echo $str;
?>
