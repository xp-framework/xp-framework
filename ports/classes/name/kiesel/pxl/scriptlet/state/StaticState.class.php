<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('name.kiesel.pxl.scriptlet.AbstractPxlState');

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
     * @access  
     * @param   
     * @return  
     */
    function process(&$request, &$response) {
      $pm= &PropertyManager::getInstance();
      $prop= &$pm->getProperties('site');
      
      $response->addFormResult(Node::fromArray($prop->readSection('site'), 'config'));
      $c= &$this->_getCatalogue();
      
      // Position defaults to last entry
      $position= $c->entries->size()- 1;
      
      // Find out if we want a specific picture
      if (
        NULL !== ($index= $request->getEnvValue('IMAGEINDEX', NULL)) &&
        intval($index) >= 0 &&
        intval($index) < $c->entries->size()
      ) {
        $position= $index- 1;
      }
      
      $entry= &$c->entries->get($position);
      if (!$entry) return;
      $page= &$this->_getPage($entry->getPath());
      
      $response->addFormResult(Node::fromObject($entry, 'current'));
      if ($position > 0) {
        $prev= &$c->entries->get($position- 1);
        $response->addFormResult(Node::fromObject($prev, 'prev'));
      }
      
      if ($position < $c->entries->size()- 1) {
        $next= &$c->entries->get($position+ 1);
        $response->addFormResult(Node::fromObject($next, 'next'));
      }
      
      
      $response->addFormResult($page->toXml());
    }
  }
?>
