<?php
/* This class is part of the XP framework
 *
 * $Id: ViewAlbumState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::dialog::scriptlet::state;

  ::uses('de.thekid.dialog.scriptlet.AbstractDialogState');

  /**
   * Handles /xml/album/view
   *
   * @purpose  State
   */
  class ViewAlbumState extends de::thekid::dialog::scriptlet::AbstractDialogState {

    /**
     * Process this state.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     * @param   &scriptlet.xml.workflow.Context context
     */
    public function process($request, $response, $context) {
      $name= $request->getQueryString();

      if ($album= $this->getAlbumFor($name)) {
        $child= $response->addFormResult(new ('album', NULL, array(
          'name'         => $album->getName(),
          'title'        => $album->getTitle(),
          'num_images'   => $album->numImages(),
          'num_chapters' => $album->numChapters(),
          'page'         => $this->getDisplayPageFor($name)
        )));
        $child->addChild(new ('description', new ($album->getDescription())));
        $child->addChild(::fromObject($album->createdAt, 'created'));
        $child->addChild(::fromArray($album->highlights, 'highlights'));
        $child->addChild(::fromArray($album->chapters, 'chapters'));

        // Check if an album is inside a collection
        if (FALSE === ($p= strpos($name, '/'))) return; 

        $parent= $this->getEntryFor(substr($name, 0, $p));
        $child->setAttribute('page', $this->getDisplayPageFor($parent->getName()));
        $child->addChild(new ('collection', NULL, array(
          'name'         => $parent->getName(),
          'title'        => $parent->getTitle()
        )));
      }
    }
  }
?>
