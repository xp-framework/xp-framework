<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses('PDFDocument', 'io.Stream', 'PDFPage', 'PDFStream');
  
  $stream= &new Stream();
  $stream->open(STREAM_MODE_WRITE);
  
  $doc= &new PDFDocument();
  $root= &$doc->getRootPage();
  $page= &new PDFPage();
  $root->addChild($page);
  $page->setContent($doc->createStream('Hello World!'));
  
  $doc->output($stream);
  
  $stream->rewind();
  Console::writeLine($stream->read($stream->size()));
  
?>
