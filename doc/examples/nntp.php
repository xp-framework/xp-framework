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
  $c= &new NntpConnection(new URL('nntp://elite.schlund.de'));
  
  try(); {
    $c->connect();
    $articleIds= $c->newNews(
      DateUtil::addDays(Date::now(), -7),
      'puretec.spass'
    );
    $c->setGroup('puretec.spass');
 
    $articles= array();   
    foreach($articleIds as $articleId) {
      $articles[]= &$c->getArticle($articleId);
    }
  } if (catch('IOException', $e)) {
    $e->printStacktrace();
    exit(-1);
  }
  
  foreach ($articles as $article) {
    Console::writeLine($article->toString());
  }
  
?>
