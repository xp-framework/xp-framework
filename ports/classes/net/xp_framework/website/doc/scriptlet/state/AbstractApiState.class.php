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
  abstract class AbstractApiState extends AbstractState {
  
    /**
     * Returns which entry to display
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @return  string entry name
     */
    protected function entryFor($request) {
      sscanf($request->getQueryString(), '%[a-zA-Z_.]', $entry);
      return $entry;
    }

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      $storage= new FileSystemDocStorage(new Folder(PropertyManager::getInstance()
        ->getProperties('storage')
        ->readString('storage', 'base')
      ));
      $entry= $this->entryFor($request);
      try {
        $response->addFormResult($storage->get($entry)->root);
      } catch (NoSuchElementException $e) {
        throw new HttpScriptletException('Entry "'.$entry.'" not found', HTTP_NOT_FOUND, $e);
      }
    }
  }
?>
