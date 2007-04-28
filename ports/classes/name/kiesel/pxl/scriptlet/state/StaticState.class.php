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
    public function process(&$request, &$response) {
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
          lastchange,
          published
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
        ($request->getEnvValue('INDEX', NULL) ? $db->prepare('and page_id= %d', $request->getEnvValue('INDEX')) : '')
      );
      
      $page= array_shift($page);
      if (!$page) throw(new IllegalStateException('No page found.'));
      
      // Find previous and next page
      $left=  $db->select('page_id, title from page where page_id < %d and published < %s', $page['page_id'], Date::now());
      $right= $db->select('page_id, title from page where page_id > %d and published < %s', $page['page_id'], Date::now());
      
      // Load all images from page
      $pictures= $db->select('
          picture_id,
          title,
          filename
        from picture as p
        where page_id= %d
        ',
        $page['page_id']
      );
      
      with ($n= $response->addFormResult(new Node('page'))); {
        $n->setAttribute('title', $page['title']);
        $n->setAttribute('id', $page['page_id']);
        
        sizeof($left) && $n->addChild(new Node('prev', NULL, array(
          'id'    => $left[0]['page_id'], 
          'title' => $this->webName($left[0]['title']))
        ));
        
        sizeof($right) && $n->addChild(new Node('next', NULL, array(
          'id'    => $right[0]['page_id'], 
          'title' => $this->webName($right[0]['title']))
        ));
        
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
