<?php
/* This class is part of the XP framework
 *
 * $Id: ViewChapterState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::dialog::scriptlet::state;

  ::uses('de.thekid.dialog.scriptlet.AbstractDialogState');

  /**
   * Handles /xml/chapter/view
   *
   * @purpose  State
   */
  class ViewChapterState extends de::thekid::dialog::scriptlet::AbstractDialogState {

    /**
     * Process this state.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     * @param   &scriptlet.xml.workflow.Context context
     */
    public function process($request, $response, $context) {
      if (2 != sscanf($request->getQueryString(), '%[^,],%d', $name, $id)) {
        throw(new lang::IllegalAccessException('Malformed query string'));
      }

      if ($album= $this->getAlbumFor($name)) {
        $child= $response->addFormResult(new ('album', NULL, array(
          'name'  => $album->getName(),
          'title' => $album->getTitle(),
          'page'  => $this->getDisplayPageFor($name)
        )));

        $chapter= $response->addFormResult(::fromObject($album->chapterAt($id), 'chapter'));
        $chapter->setAttribute('id', $id+ 1);
        $chapter->setAttribute('previous', $id == 0 ? NULL : $id - 1);
        $chapter->setAttribute('next', $id == $album->numChapters() - 1 ? NULL : $id + 1);

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
