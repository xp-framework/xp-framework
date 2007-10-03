<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'io.FileUtil',
    'io.File',
    'de.thekid.dialog.Album',
    'de.thekid.dialog.Update',
    'de.thekid.dialog.SingleShot',
    'de.thekid.dialog.EntryCollection',
    'util.PropertyManager'
  );

  /**
   * Abstract base class
   *
   * @purpose  Base class
   */
  class AbstractDialogState extends AbstractState {
    public
      $dataLocation = '';
    
    /**
     * Returns index
     *
     * @param   int i default 0 page number
     * @return  string[]
     */
    public function getIndexPage($i= 0) {
      return unserialize(FileUtil::getContents(new File($this->dataLocation.'page_'.$i.'.idx')));
    }
    
    /**
     * Retrieves which page a given element is on
     *
     * @param   string name
     * @return  int
     */
    public function getDisplayPageFor($name) {
      return unserialize(FileUtil::getContents(new File($this->dataLocation.$name.'.idx')));
    }
    
    /**
     * Helper method
     *
     * @param   string name
     * @param   string expect expected type
     * @return  de.thekid.dialog.IEntry
     * @throws  lang.IllegalArgumentException if found entry is not of expected type
     */
    protected function _getEntryFor($name, $expect) {
      $entry= unserialize(FileUtil::getContents(new File($this->dataLocation.$name.'.dat')));

      // Check expectancy
      if (!is($expect, $entry)) throw new IllegalArgumentException(sprintf(
        'Entry of type %s found, %s expected',
        xp::typeOf($entry),
        $expect
      ));

      return $entry;
    }

    /**
     * Returns entry for a specified name
     *
     * @param   string name
     * @return  de.thekid.dialog.IEntry
     */
    public function getEntryFor($name) {
      return $this->_getEntryFor($name, 'de.thekid.dialog.IEntry');
    }

    /**
     * Returns album for a specified name
     *
     * @param   string name
     * @return  de.thekid.dialog.Album
     * @throws  lang.IllegalArgumentException if it is not an album
     */
    public function getAlbumFor($name) {
      return $this->_getEntryFor($name, 'de.thekid.dialog.Album');
    }
    
    /**
     * Set up this state
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     * @param   scriptlet.xml.Context context
     */
    public function setup($request, $response, $context) {
      parent::setup($request, $response, $context);
      
      // Read configuration and add relevant sections to formresult
      with ($prop= PropertyManager::getInstance()->getProperties($request->getProduct())); {
        $this->dataLocation= $prop->readString(
          'data',
          'location',
          $request->getEnvValue('DOCUMENT_ROOT').'/../data/'
        );
        
        $response->addFormresult(Node::fromArray($prop->readSection('general'), 'config'));
        $links= $response->addFormresult(new Node('links'));
        foreach ($prop->readSection('links') as $name => $url) {
          $links->addChild(new Node('link', NULL, array('id' => $name, 'href' => $url)));
        }
      }
    }  
  }
?>
