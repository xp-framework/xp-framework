<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('de.thekid.dialog.scriptlet.AbstractDialogState');

  /**
   * Handles /xml/album/view
   *
   * @purpose  State
   */
  class ViewAlbumState extends AbstractDialogState {

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     * @param   &scriptlet.xml.workflow.Context context
     */
    function process(&$request, &$response, &$context) {
      $name= $request->getQueryString();

      if ($album= &$this->getAlbumFor($name)) {
        $child= &$response->addFormResult(new Node('album', NULL, array(
          'name'         => $album->getName(),
          'title'        => $album->getTitle(),
          'num_images'   => $album->numImages(),
          'num_chapters' => $album->numChapters(),
          'page'         => $this->getDisplayPageFor($name)
        )));
        $child->addChild(new Node('description', new PCData($album->getDescription())));
        $child->addChild(Node::fromObject($album->createdAt, 'created'));
        $child->addChild(Node::fromArray($album->highlights, 'highlights'));
        $child->addChild(Node::fromArray($album->chapters, 'chapters'));
      }
    }
  }
?>
