<?php
/* This class is part of the XP framework
 *
 * $Id: WebsiteScriptlet.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  uses(
    'net.xp_framework.util.markup.MarkupBuilder',
    'scriptlet.HttpScriptlet',
  	'xml.rdf.RDFNewsFeed'
  );

  /**
   * Website scriptlet for http://xp-framework.info/
   *
   * @see      http://xp-framework.info/
   * @purpose  Scriptlet
   */
  class RssFeedScriptlet extends HttpScriptlet {
    const
      MASTER_CATEGORY  = 8;
      
    /**
     *
     */
    public function fetchCategory($db, $id) {
      return current($db->select('
      	  categoryid,
      	  category_name
      	from
      	  serendipity_category
      	where categoryid= %d
      	',
      	$id
      ));
    }
    
    protected function sanitizeHref($name) {
      return preg_replace('#[^a-zA-Z0-9\-\._]#', '_', $name);
    }
    
    /**
     * Processes the request
     *
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     */
    public function fetchEntries($db, $id) {
      return $db->query('
        select 
          entry.id as id,
          entry.title as title,
          entry.body as body,
          entry.author as author,
          entry.timestamp as timestamp,
          length(entry.extended) as extended_length,
          category.categoryid as category_id,
          category.category_name as category
        from
          serendipity_entries entry,
          serendipity_entrycat matrix,
          serendipity_category category
        where
          (category.parentid = %1$d or category.categoryid = %1$d)
          and entry.isdraft = "false"
          and entry.id = matrix.entryid
          and matrix.categoryid = category.categoryid
        order by
          timestamp desc
        limit 0, 20
        ',
        $id
      );
    }
  
    public function doGet($request, $response) {
      $db= ConnectionManager::getInstance()->getByHost('news', 0);
      $categoryid= $request->getParam('c', self::MASTER_CATEGORY);
      
      $category= $this->fetchCategory($db, $categoryid);
      $q= $this->fetchEntries($db, $categoryid);
      
      $feed= new RDFNewsFeed();
      $url= $request->getURL();
      
      $feed->setChannel(
      	$category['category_name'],
      	sprintf('%s://%s/%d/%s',
      	  $url->getScheme(),
      	  $url->getHost(),
      	  $categoryid,
      	  $this->sanitizeHref($category['category_name'])
      	)
      );
      
      // Add items to feed, build markup
      $markupBuilder= new MarkupBuilder();
      while ($q && $r= $q->next()) {
      	$feed->addItem(
      	  $r['title'],
      	  sprintf('%s://%s/article/%d/%s/%s',
      	    $url->getScheme(),
      	    $url->getHost(),
      	    $r['id'],
      	    create(new Date($r['timestamp']))->toString('Y/m/d'),
      	    $this->sanitizeHref($r['title'])
      	  ),
      	  $markupBuilder->markupFor($r['body']),
      	  new Date($r['timestamp'])
      	);
      }
      
      // Write out prepared tree
      $response->setHeader('Content-type', 'application/xml; charset=iso-8859-1');
      
      $rdf= $feed->getDeclaration()."\n".$feed->getSource(0);
      $response->setHeader('Content-length', strlen($rdf));
      $response->write($rdf);
    }
  }
?>