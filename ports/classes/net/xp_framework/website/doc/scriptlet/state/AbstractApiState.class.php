<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.AbstractState', 
    'util.PropertyManager',
    'io.Folder',
    'net.xp_framework.website.doc.build.storage.FileSystemDocStorage'
  );

  /**
   * Abstract base class for all classes reading generated api documentation
   * from serialized files in the filesystem.
   *
   * @purpose  State
   */
  class AbstractApiState extends AbstractState {

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      sscanf($request->getQueryString(), '%[a-zA-Z_.]', $entry);
      
      $storage= new FileSystemDocStorage(new Folder(PropertyManager::getInstance()
        ->getProperties('storage')
        ->readString('storage', 'base')
      ));
      
      try {
        $response->addFormResult($storage->get($entry)->root);
      } catch (NoSuchElementException $e) {
        throw new HttpScriptletException('Entry "'.$entry.'" not found', HTTP_NOT_FOUND, $e);
      }
    }
  }
?>
