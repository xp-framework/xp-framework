<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'name.kiesel.pxl.util.PageCreator',
    'name.kiesel.pxl.storage.FilesystemContainer'
  );
  
  // {{{ main
  $p= &new ParamString();
  
  $title= $p->value('title', 't');
  $picturefiles= array();
  for ($i= 1; $i < $p->count; $i++) {
    if (preg_match('/.*\.(jpg|jpeg|png|gif)$/', $p->value($i))) {
      $picturefiles[]= $p->value($i);
    }
  }

  $s= &new FilesystemContainer(dirname(__FILE__).'/../doc_root/pages/');
  $pc= &new PageCreator(
    $s, 
    $title,
    $picturefiles
  );
  $p->exists('date') && $pc->setDate(Date::fromString($p->value('date')));
  $pc->addPage();
  
  // }}} 
?>
