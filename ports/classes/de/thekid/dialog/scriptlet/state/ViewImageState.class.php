<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('de.thekid.dialog.scriptlet.AbstractDialogState');

  /**
   * Handles /xml/image/view
   *
   * @purpose  State
   */
  class ViewImageState extends AbstractDialogState {

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     * @param   &scriptlet.xml.workflow.Context context
     */
    function process(&$request, &$response, &$context) {
      if (4 != sscanf($request->getQueryString(), '%[^,],%1s,%d,%d', $name, $type, $chapter, $id)) {
        return throw(new IllegalAccessException('Malformed query string'));
      }
      
      if ($album= &$this->getAlbumFor($name)) {
        $child= &$response->addFormResult(new Node('album', NULL, array(
          'name'  => $album->getName(),
          'title' => $album->getTitle(),
          'page'  => $this->getDisplayPageFor($name)
        )));
        $child->addChild(new Node('description', new PCData($album->getDescription())));
        $child->addChild(Node::fromObject($album->createdAt, 'created'));
        
        switch ($type) {
          case 'h':
            $selected= &$response->addFormResult(Node::fromObject($album->highlightAt($id), 'selected'));
            $selected->setAttribute('last', $id >= $album->numHighlights()- 1);
            break;

          case 'i':
            $selected= &$response->addFormResult(Node::fromObject($album->chapters[$chapter]->imageAt($id), 'selected'));
            $selected->setAttribute('last', $id >= $album->chapters[$chapter]->numImages()- 1);
            break;
        }

        $selected->setAttribute('id', $id);
        $selected->setAttribute('type', $type);
        $selected->setAttribute('chapter', $chapter);
      }
    }
  }
?>
