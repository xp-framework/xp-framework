<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('de.thekid.dialog.scriptlet.AbstractDialogState');

  /**
   * Handles /xml/static
   *
   * @purpose  State
   */
  class StaticState extends AbstractDialogState {

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     * @param   &scriptlet.xml.workflow.Context context
     */
    function process(&$request, &$response, &$context) {
      sscanf($request->getQueryString(), 'page%d', $page);
      $index= $this->getIndexPage((int)$page);
      
      // Add paging information
      $response->addFormResult(new Node('pager', NULL, array(
        'offset'  => $page,
        'total'   => $index[0]
      )));
      
      // Add albums from index
      $node= &$response->addFormResult(new Node('albums'));
      foreach ($index[1] as $name) {
        if ($album= &$this->getAlbumFor($name)) {
          $child= &$node->addChild(new Node('album', NULL, array(
            'name'  => $album->getName(),
            'title' => $album->getTitle()
          )));
          $child->addChild(new Node('description', new PCData($album->getDescription())));
          $child->addChild(Node::fromObject($album->createdAt, 'created'));
          $child->addChild(Node::fromArray($album->highlights, 'highlights'));
        }
      }
    }
  }
?>
