<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace name::kiesel::pxl::scriptlet;

  ::uses(
    'util.Date',
    'scriptlet.HttpScriptlet',
    'xml.rdf.RDFNewsFeed',
    'name.kiesel.pxl.db.PxlPage',
    'name.kiesel.pxl.db.finder.PxlPageFinder'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class RssFeedScriptlet extends scriptlet::HttpScriptlet {

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function linkFor($page) {
      return sprintf('%s/story/%d/%s',
        rtrim(util::PropertyManager::getInstance()->getProperties('site')->readString('site', 'url'), '/'),
        $page->getPage_id(),
        $page->getTitle()
      );
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function doGet($request, $response) {
      $finder= new name::kiesel::pxl::db::finder::PxlPageFinder();
      $iterator= $finder->iterate($finder->mostRecent());

      $prop= util::PropertyManager::getInstance()->getProperties('site');

      // Create RSS feed
      $feed= new xml::rdf::RDFNewsFeed();
      $feed->setChannel(
        $prop->readString('site', 'title'),
        $prop->readString('site', 'url'),
        $prop->readString('site', 'description', '')
      );
      
      $cnt= 0;
      while ($iterator->hasNext() && $cnt++ < 25) {
        $page= $iterator->next();
        
        $feed->addItem(
          $page->getTitle(),
          $this->linkFor($page),
          $page->getDescription(),
          util::Date::fromString($page->getPublished())
        );
      }
      
      $tree= $feed->getDeclaration()."\n".$feed->getSource(0);
      $response->setContentType('xml/application+rss');
      $response->setContentLength(strlen($tree));
      
      $response->write($tree);
    }
  }
?>
