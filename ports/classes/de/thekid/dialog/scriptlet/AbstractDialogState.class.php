<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'io.FileUtil',
    'io.File',
    'de.thekid.dialog.Album'
  );

  /**
   * Abstract base class
   *
   * @purpose  Base class
   */
  class AbstractDialogState extends AbstractState {
    var
      $dataLocation = '';
    
    /**
     * Returns index
     *
     * @access  protected
     * @return  string[]
     */
    function getIndex() {
      try(); {
        $index= unserialize(FileUtil::getContents(new File($this->dataLocation.'index')));
      } if (catch('IOException', $e)) {
        return throw($e);
      }
      return $index;
    }
    
    /**
     * Returns album for a specified name
     *
     * @access  protected
     * @param   string name
     * @return  &de.thekid.dialog.Album
     */
    function &getAlbumFor($name) {
      try(); {
        $album= &unserialize(FileUtil::getContents(new File($this->dataLocation.$name.'.dat')));
      } if (catch('IOException', $e)) {
        return throw($e);
      }
      return $album;
    }
    
    /**
     * Set up this state
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     * @param   &scriptlet.xml.Context context
     */
    function setup(&$request, &$response, &$context) {
      parent::setup($request, $response, $context);
      
      // FIXME make configurable?
      $this->dataLocation= $request->getEnvValue('DOCUMENT_ROOT').'/../data/';
    }  
  }
?>
