<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'name.kiesel.pxl.scriptlet.AbstractPxlState',
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
      
      $page= $db->select('
          page_id,
          title,
          description,
          author_id,
          sequence,
          lastchange,
          cast(published, "date") published
        from
          page
        where published is not null
          and published < %s
          %c
        order by 
          page_id desc
        limit 1
        ',
        Date::now(),
        ($request->getEnvValue('INDEX', NULL) ? $db->prepare('and sequence= %d', $request->getEnvValue('INDEX')) : '')
      );
      
      $page= current($page);
      if (!$page) throw new IllegalStateException('No page found.');
      
      // Find previous and next page
      $left=  $db->select('page_id, title, cast(published, "date") as published from page where sequence= %d and published is not null', $page['sequence']- 1, Date::now());
      $right= $db->select('page_id, title, cast(published, "date") as published from page where sequence= %d and published is not null', $page['sequence']+ 1, Date::now());
      
      // Load all images from page
      $pictures= $db->select('
          picture_id,
          filename
        from picture as p
        where page_id= %d
        ',
        $page['page_id']
      );
      
      $comments= $db->select('
          comment_id,
          comment_type_id,
          title,
          body,
          url,
          author,
          email,
          cast(commented_at, "date") as commented_at
        from
          comment
        where page_id= %d
          and bz_id= 20000
        ',
        $page['page_id']
      );
      
      with ($n= $response->addFormResult(new Node('page'))); {
        $n->setAttribute('title', $page['title']);
        $n->setAttribute('id', $page['page_id']);

        sizeof($left) && $n->addChild(new Node('prev', NULL, array(
          'id'        => $left[0]['sequence'],              
          'title'     => $this->webName($left[0]['title']), 
          'published' => $left[0]['published']->toString('Y/m/d')
        )));

        sizeof($right) && $n->addChild(new Node('next', NULL, array(
          'id'        => $right[0]['page_id'],               
          'title'     => $this->webName($right[0]['title']), 
          'published' => $right[0]['published']->toString('Y/m/d')
        )));
        
        $n->addChild(new Node('description', new PCData($page['description'])));
      }
      
      $pnode= $n->addChild(new Node('pictures'));
      foreach ($pictures as $p) {
        $tmp= $pnode->addChild(Node::fromArray($p, 'picture'));
        
        try {
          $tmp->addChild(Node::fromObject(
            ExifData::fromFile(new File($request->getEnvValue('DOCUMENT_ROOT').'/pages/'.$page['page_id'].'/'.$p['filename'])),
            'exif'
          ));
        } catch(ImagingException $ignored) {
          // ... some images might have no exif data
        }
        
        list($width, $height)= getimagesize($request->getEnvValue('DOCUMENT_ROOT').'/pages/'.$page['page_id'].'/'.$p['filename']);
        $tmp->addChild(new Node('dimensions', NULL, array(
          'width'   => $width, 
          'height'  => $height
        )));
      }
    }
  }
?>
