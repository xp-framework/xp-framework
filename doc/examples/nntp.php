<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'peer.news.NntpConnection',
    'util.Date',
    'util.DateUtil'    
  );
  
  // {{{ main
  $c= &new NntpConnection(new URL('nntp://news.php.net'));
  try(); {
    $c->connect();
    $c->setGroup('php.version4');
    
    $articles= array($c->getArticle());
    
    for ($i= 0; $i<9; $i++) {
      $articles[]= $c->getNextArticle();
    }
  } if (catch('IOException', $e)) {
    $e->printStacktrace();
    exit(-1);
  }
  
  foreach ($articles as $article) {
    Console::writeLine($article->toString());
  }
  
?>
