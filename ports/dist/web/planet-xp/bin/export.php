<?php
/*
 * This file is part of the XP framework's ports
 *
 * $Id$
 */
  
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.log.Logger',
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'io.File',
    'io.FileUtil',
    'xml.Tree'
  );
  
  /// {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../etc');
  
  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));
  
  $cm= &ConnectionManager::getInstance();
  $cm->configure($pm->getProperties('database'));
  
  $param= &new ParamString();
  $syndicate_id= ($param->exists('syndicate') ? intval($param->value('syndicate')) : 1);
  
  /// {{{ main
  try(); {
    $db= &$cm->getByHost('syndicate', 0);
    
    // Load most recent entries from any blog
    $items= $db->select('
        i.feeditem_id,
        i.title,
        i.content,
        i.link,
        i.author,
        i.published,
        f.feed_id,
        f.title as feedtitle,
        f.link as feedlink,
        f.description
      from
        syndicate.feeditem i,
        syndicate.feed f,
        syndicate.syndicate_feed_matrix sfm
      where f.feed_id= i.feed_id
        and f.feed_id= sfm.feed_id
        and sfm.syndicate_id= %d
      order by published desc
      limit 0,100',
      $syndicate_id
    );

    // Load all feeds
    $feeds= $db->select('
        f.feed_id,
        f.title,
        f.link,
        f.description
      from
        syndicate.feed f,
        syndicate_feed_matrix sfm
      where f.feed_id= sfm.feed_id
        and sfm.syndicate_id= %d',
      $syndicate_id
    );
  } if (catch('SQLException', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  $doc= &new Tree('syndicate');
  $doc->root->setAttribute('id', $syndicate_id);
  
  foreach ($items as $item) {
    Console::writeLinef('---> Adding item #%d', $item['feeditem_id']);
    $child= &$doc->addChild(new Node('item', NULL, array(
      'feeditem_id' => $item['feeditem_id'],
      'title'       => $item['title'],
      'link'        => $item['link'],
      'author'      => $item['author']
    )));
    
    $child->addChild(new Node('content', new PCData(str_replace('&', '&amp;', $item['content']))));
    $child->addChild(Node::fromObject($item['published'], 'published'));
    
    // Information about the feed
    $child->addChild(new Node('feed', NULL, array(
      'feed_id'     => $item['feed_id'],
      'title'       => $item['feedtitle'],
      'link'        => $item['feedlink'],
      'description' => $item['description']
    )));
  }
  
  // Store tree to file (ommitting the XML declaration is intentional)
  FileUtil::setContents(
    new File(sprintf(dirname(__FILE__).'/../cache/syndicate-%d.xml', $syndicate_id)),
    $doc->getSource(0)
  );
  
  $fdoc= &new Tree('syndicate');
  $fdoc->root->setAttribute('id', $syndicate_id);
  
  foreach ($feeds as $feed) {
    $fdoc->addChild(new Node('feed', $feed['description'], array(
      'feed_id' => $feed['feed_id'],
      'title'   => $feed['title']
    )));
  }
  
  FileUtil::setContents(
    new File(sprintf(dirname(__FILE__).'/../cache/syndicated-feeds-%d.xml', $syndicate_id)),
    $fdoc->getSource(0)
  );
  /// }}}  
?>
