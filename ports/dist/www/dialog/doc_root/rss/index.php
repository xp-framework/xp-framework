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
    try(); {
      $entry= &unserialize(FileUtil::getContents(new File(DATALOCATION.$name.'.dat')));
    } if (catch('IOException', $e)) {
      return throw($e);
    }
    
    return $entry;
  }
  // }}}
  
  // {{{ function getIndexFor($page
  function getIndexFor($i= 0) {
    try(); {
      $index= unserialize(FileUtil::getContents(new File(DATALOCATION.'page_'.$i.'.idx')));
    } if (catch('IOException', $e)) {
      return throw($e);
    }
    return $index;
  }
  // }}}
  
  // {{{ function urlFor(&$item)
  function urlFor(&$item) {
    $base= sprintf('http://%s/xml/%s.%s', $_SERVER['HTTP_HOST'], $_SERVER['DEF_PROD'], $_SERVER['DEF_LANG']);
    switch (get_class($item)) {
      case 'album':
        return sprintf('%s/album/view?%s', $base, $item->getName());
        
      case 'update': 
        return sprintf('%s/album/view?%s', $base, $item->getAlbumName());      
        
      case 'singleshot':
        return sprintf('%s/shot/view?%s,0', $base, $item->getName());
      
      case 'entrycollection':
        return urlFor($item->entryAt(0));
    }
  }
  // }}}
  
  // {{{ function addAlbumItem(rdf, item)
  function addAlbumItem(&$rdf, &$item) {
    $rdf->addItem(
      $item->getTitle(),
      urlFor($item),
      $item->getDescription(),
      $item->getCreatedAt()
    );
  }
  // }}}
  
  // {{{ function addUpdateItem(rdf, item)
  function addUpdateItem(&$rdf, &$item) {
    $rdf->addItem(
      $item->getTitle(),
      urlFor($item),
      $item->getDescription(),
      $item->getDate()
    );
  }
  // }}}
  
  // {{{ function addSingleShotItem(rdf, item)
  function addSingleShotItem(&$rdf, &$item) {
    $rdf->addItem(
      $item->getTitle(),
      urlFor($item),
      $item->getDescription(),
      $item->getDate()
    );
  }
  // }}}

  // {{{ function addEntryCollectionItem(rdf, item)
  function addEntryCollectionItem(&$rdf, &$item) {
    $rdf->addItem(
      $item->getTitle(),
      urlFor($item),
      $item->getDescription(),
      $item->getCreatedAt()
    );
  }
  // }}}
  
  // {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../../etc');
  $prop= &$pm->getProperties('dialog');

  define('DATALOCATION',  $prop->readString('data', 'location', dirname(__FILE__).'/../../data/'));
  
  // Load index of first two pages
  try(); {
    $index= getIndexFor(0);
    $entries= $index['entries'];
  } if (catch('IOException', $e)) {
    echo $e->getMessage();
    exit(1);
  }
  
  try(); {
    $index= getIndexFor(1);
    $index && $entries= array_merge($entries, $index['entries']);
  } if (catch('IOException', $ignored)) {
  }

  // Find date of newest entry
  $lastChange= Date::now();
  $entry= &getEntryFor(current($index['entries']));
  $lastChange= &$entry->getDate();
  
  $rdf= &new RDFNewsFeed();
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
  
  foreach (array_reverse($entries) as $name) {
    try(); {
      $entry= &getEntryFor($name);
    } if (catch('IOException', $e)) {
      echo $e->toString();
      exit;
    }

    switch (get_class($entry)) {
      case 'album': addAlbumItem($rdf, $entry); break;
      case 'update': addUpdateItem($rdf, $entry); break;
      case 'singleshot': addSingleShotItem($rdf, $entry); break;
      case 'entrycollection': addEntryCollectionItem($rdf, $entry); break;
      default: break;
    }
  }

  header('Content-type: text/xml');
  echo $rdf->getDeclaration()."\n".$rdf->getSource(0);
  // }}}
?>
