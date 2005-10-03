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
   * Base class for all mono states.
   *
   * @purpose  Mono base state
   */
  class AbstractMonoState extends AbstractState {

    /**
     * Constructor.
     *
     * @access  public
     */
    function __construct() {
      $pm= &PropertyManager::getInstance();
      $this->config= &$pm->getProperties('mono');
    }

    /**
     * Load the catalog index from the file system. Caches
     * it for repeated access.
     *
     * @access  public
     * @return  &de.document-root.mono.MonoCatalog
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
     * Retrieve the latest shot id.
     *
     * @access  public
     * @return  int
     */
    function getLast_id() {
      $catalog= &$this->_getCatalog();
      return $catalog->getCurrent_id();
    }
    
    /**
     * Loads the picture from the file system by its id.
     *
     * @access  public
     * @param   int id
     * @return  &de.document-root.mono.MonoPicture
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
     * Loads the comments for the given shot from the filesystem.
     *
     * @access  public
     * @param   int id
     * @return  &de.document.root.MonoPictureComments
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
     * Process this state. Adds the default values from the configuration
     * to the XML result tree.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
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
