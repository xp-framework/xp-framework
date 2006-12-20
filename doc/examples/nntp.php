<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('peer.news.NntpConnection');
  
  // {{{ main
  $p= new ParamString();
  list($server, $newsgroup)= explode('/', $p->value(1, NULL, 'news.php.net/php.version4'));
  
  $c= new NntpConnection(new URL('nntp://'.$server));
  try {
    $c->connect();
    $c->setGroup($newsgroup);

    $articles= array($c->getArticle());
    
    for ($i= 0; $i < 9; $i++) {
      $articles[]= $c->getNextArticle();
    }
  } catch (IOException $e) {
    $e->printStacktrace();
    exit(-1);
  }
  
  foreach ($articles as $article) {
    Console::writeLine($article->toString());
  }
  // }}}
?>
