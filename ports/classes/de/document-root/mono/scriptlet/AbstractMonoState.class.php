<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'io.FileUtil',
    'util.Date',
    'de.document-root.mono.MonoCatalog',
    'de.document-root.mono.MonoPicture'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AbstractMonoState extends AbstractState {

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
      $pm= &PropertyManager::getInstance();
      $this->config= &$pm->getProperties('mono');
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &_getCatalog() {
      static $catalog= NULL;
      
      if (NULL === $catalog) {
        try(); {
          $catalog= unserialize(FileUtil::getContents(
            new File(sprintf('%s/../data/dates.idx', 
              rtrim($_SERVER['DOCUMENT_ROOT'], '/'))
          )));
        } if (catch('IOException', $e)) {
          return throw($e);
        }
      }
      
      return $catalog;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getLast_id() {
      $catalog= &$this->_getCatalog();
      return $catalog->getCurrent_id();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &getPictureById($id) {
      try(); {
        $picture= unserialize(FileUtil::getContents(
          new File(sprintf('%s/shots/%d/picture.idx',
            rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR),
            $id
        ))));
      } if (catch('IOException', $e)) {
        return throw($e);
      }
      
      return $picture;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &getPictureCommentsById($id) {
      $f= &new File(sprintf('%s/../data/%d/comments.dat',
        rtrim($_SERVER['DOCUMENT_ROOT'], '/'),
        $id
      ));
      if (!$f->exists()) return NULL;
    
      try(); {
        $description= unserialize(FileUtil::getContents($f));
      } if (catch('IOException', $e)) {
        return throw($e);
      }
      
      return $description;
    }    
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function process(&$request, &$response, &$context) {
    
      // Add the configuration to the result tree
      $cNode= &$response->addFormResult(new Node('config'));
      $section= $this->config->getFirstSection();
      if ($section) { 
        do {
          $cNode->addChild(Node::fromArray($this->config->readSection($section), $section));
        } while ($section= $this->config->getNextSection());
      }
    }  
  }
?>
