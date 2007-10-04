<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  uses(
    'io.File',
    'io.FileUtil',
    'xml.rdf.RDFNewsFeed',
    'util.PropertyManager',
    'de.thekid.dialog.Album',
    'de.thekid.dialog.Update',
    'de.thekid.dialog.SingleShot',
    'de.thekid.dialog.EntryCollection'
  );
  
  // {{{ function getEntryFor($name)
  function getEntryFor($name) {
    $entry= unserialize(FileUtil::getContents(new File(DATALOCATION.$name.'.dat')));
    return $entry;
  }
  // }}}
  
  // {{{ function getIndexFor($page
  function getIndexFor($i= 0) {
    $index= unserialize(FileUtil::getContents(new File(DATALOCATION.'page_'.$i.'.idx')));
    return $index;
  }
  // }}}
  
  // {{{ function urlFor($item)
  function urlFor($item) {
    $base= sprintf('http://%s', $_SERVER['HTTP_HOST']);
    switch (get_class($item)) {
      case 'Album':
        return sprintf('%s/album/%s', $base, $item->getName());
        
      case 'Update': 
        return sprintf('%s/album/%s', $base, $item->getAlbumName());      
        
      case 'SingleShot':
        return sprintf('%s/shot/%s/0', $base, $item->getName());
      
      case 'EntryCollection':
        return urlFor($item->entryAt(0));
    }
  }
  // }}}
  
  // {{{ function addAlbumItem(rdf, item)
  function addAlbumItem($rdf, $item) {
    $rdf->addItem(
      $item->getTitle(),
      urlFor($item),
      $item->getDescription().'<br/><img border="1" src="/albums/'.$item->getName().'/thumb.'.$item->highlightAt(0)->getName().'"/>',
      $item->getCreatedAt()
    );
  }
  // }}}
  
  // {{{ function addUpdateItem(rdf, item)
  function addUpdateItem($rdf, $item) {
    $rdf->addItem(
      $item->getTitle(),
      urlFor($item),
      $item->getDescription(),
      $item->getDate()
    );
  }
  // }}}
  
  // {{{ function addSingleShotItem(rdf, item)
  function addSingleShotItem($rdf, $item) {
    $rdf->addItem(
      $item->getTitle(),
      urlFor($item),
      $item->getDescription().'<br/><img border="1" src="/shots/thumb.color.'.$item->getName().'.jpg"/>',
      $item->getDate()
    );
  }
  // }}}

  // {{{ function addEntryCollectionItem(rdf, item)
  function addEntryCollectionItem($rdf, $item) {
    $rdf->addItem(
      $item->getTitle(),
      urlFor($item),
      $item->getDescription().'<br/><img border="1" src="/albums/'.$item->entryAt(0)->getName().'/thumb.'.$item->entryAt(0)->highlightAt(0)->getName().'"/>',
      $item->getCreatedAt()
    );
  }
  // }}}
  
  // {{{ main
  $pm= PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../../etc');
  $prop= $pm->getProperties('dialog');

  define('DATALOCATION', $prop->readString('data', 'location', dirname(__FILE__).'/../../data/'));
  
  // Load index of first two pages
  $index= array();
  $index[0]= getIndexFor(0);
  $entries= $index[0]['entries'];

  try {
    $index[1]= getIndexFor(1);
    $index[1] && $entries= array_merge($entries, $index[1]['entries']);
  } catch(IOException $ignored) {
  }

  // Find date of newest entry
  $lastChange= Date::now();
  $entry= getEntryFor(current($index[0]['entries']));
  $lastChange= $entry->getDate();
  
  $rdf= new RDFNewsFeed();
  $rdf->setChannel(
    $prop->readString('general', 'title', 'Dialog'),
    'http://'.$_SERVER['HTTP_HOST'].'/',
    $prop->readString('general', 'title', 'Dialog'),
    $lastChange,
    $prop->readString('general', 'language', 'en_US'),
    $prop->readString('general', 'creator', ''),
    $prop->readString('general', 'publisher', ''),
    $prop->readString('general', 'copyright', '')
  );
  
  foreach ($entries as $name) {
    $entry= getEntryFor($name);

    switch (get_class($entry)) {
      case 'Album': addAlbumItem($rdf, $entry); break;
      case 'Update': addUpdateItem($rdf, $entry); break;
      case 'SingleShot': addSingleShotItem($rdf, $entry); break;
      case 'EntryCollection': addEntryCollectionItem($rdf, $entry); break;
      default: break;
    }
  }

  header('Content-type: text/xml');
  echo $rdf->getDeclaration()."\n".$rdf->getSource(0);
  // }}}
?>
