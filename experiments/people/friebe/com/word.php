<?php
/* This file is part of the XP framework's experiments
 *
 * $Id: reflection.php 8323 2006-11-05 15:26:43Z friebe $
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('ActiveX');
  
  // {{{ main
  try(); {
    $word= &ActiveX::forName('Word.Application');
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  Console::writeLine('Using ', $word->toString());

  $word->var->Documents->Add();
  $word->var->Selection->TypeText('Test string');
  $word->var->Documents[1]->SaveAs('Test.doc');
  $word->Quit();
  // }}}
?>
