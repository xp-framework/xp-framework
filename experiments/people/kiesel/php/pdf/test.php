<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses('PDFDocument', 'io.Stream');
  
  $stream= &new Stream();
  $stream->open(STREAM_MODE_WRITE);
  
  $doc= &new PDFDocument();
  $doc->output($stream);
  
  $stream->rewind();
  Console::writeLine($stream->read($stream->size()));
  
?>
