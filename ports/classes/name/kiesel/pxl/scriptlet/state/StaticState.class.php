<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'name.kiesel.pxl.scriptlet.AbstractPxlState',
    'name.kiesel.pxl.format.FormatterChain',
    'name.kiesel.pxl.db.PxlPage',
    'name.kiesel.pxl.db.PxlPicture',
    'name.kiesel.pxl.db.PxlTag',
    'name.kiesel.pxl.db.PxlComment',
    'rdbms.Statement',
    'img.util.ExifData'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class StaticState extends AbstractPxlState {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function process($request, $response) {
      parent::process($request, $response);
      $db= ConnectionManager::getInstance()->getByHost('pxl', 0);
      
      // Find out if we want a specific picture
      if (NULL !== ($index= $request->getEnvValue('INDEX', NULL))) {
        $position= $index- 1;
      }
      
      $page= current(PxlPage::getPeer()->doSelect(new Statement('
        select
          page_id,
          title,
          description,
          author_id,
          permalink,
          sequence,
          published,
          lastchange,
          changedby
        from
          page
        where published is not null
          and published < %s
          %c
        order by 
          sequence desc
        limit 1
        ',
        Date::now(),
        ($request->getEnvValue('INDEX', NULL) ? $db->prepare('and page_id= %d', $request->getEnvValue('INDEX')) : '')
      )));
      
      if (!$page instanceof PxlPage) throw new IllegalStateException('No page found.');
      
      // Find previous and next page
      $left=  $db->select('title, permalink from page where sequence= %d and published < %s', $page->getSequence()- 1, Date::now());
      $right= $db->select('title, permalink from page where sequence= %d and published < %s', $page->getSequence()+ 1, Date::now());
      $latest= $db->select('title, permalink from page where sequence= (select max(sequence) from page where published < %s)', Date::now());
      
      // Load all images from page
      $pictures= PxlPicture::getByPage_id($page->getPage_id());
      
      // TBI: Load comments
      // $comments= PxlComment::getByPage_id($page->getPage_id());
      
      with ($n= $response->addFormResult(new Node('page'))); {
        $n->setAttribute('title', $page->getTitle());
        $n->setAttribute('id', $page->getPage_id());

        sizeof($left) && $n->addChild(new Node('prev', NULL, array(
          'title'     => $this->webName($left[0]['title']),
          'link'      => $left[0]['permalink']
        )));

        sizeof($right) && $n->addChild(new Node('next', NULL, array(
          'id'        => $right[0]['page_id'],               
          'link'      => $right[0]['permalink']
        )));
        
        $n->addChild(new Node('latest', NULL, array(
          'title'     => $this->webName($latest[0]['title']), 
          'link'      => $latest[0]['permalink']
        )));
        
        $formatter= new FormatterChain();
        $n->addChild(new Node('description', new PCData($formatter->apply($page->getDescription()))));
        $n->addChild(Node::fromObject(Date::fromString($page->getPublished()), 'published'));
      }
      
      $pnode= $n->addChild(new Node('pictures'));
      foreach ($pictures as $p) {
        $tmp= $pnode->addChild(Node::fromObject($p, 'picture'));
        
        try {
          $tmp->addChild(Node::fromObject(
            ExifData::fromFile(new File($request->getEnvValue('DOCUMENT_ROOT').'/pages/'.$page->getPage_id().'/'.$p->getFilename())),
            'exif'
          ));
        } catch(ImagingException $ignored) {
          // ... some images might have no exif data
        }
        
        list($width, $height)= getimagesize($request->getEnvValue('DOCUMENT_ROOT').'/pages/'.$page->getPage_id().'/'.$p->getFilename());
        $tmp->addChild(new Node('dimensions', NULL, array(
          'width'   => $width, 
          'height'  => $height
        )));
      }
    }
  }
?>
