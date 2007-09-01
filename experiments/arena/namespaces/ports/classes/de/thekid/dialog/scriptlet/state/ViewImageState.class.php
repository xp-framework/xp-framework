<?php
/* This class is part of the XP framework
 *
 * $Id: ViewImageState.class.php 10768 2007-07-10 20:40:29Z kiesel $ 
 */

  namespace de::thekid::dialog::scriptlet::state;

  ::uses('de.thekid.dialog.scriptlet.AbstractDialogState');

  /**
   * Handles /xml/image/view
   *
   * @purpose  State
   */
  class ViewImageState extends de::thekid::dialog::scriptlet::AbstractDialogState {

    /**
     * Process this state.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     * @param   &scriptlet.xml.workflow.Context context
     */
    public function process($request, $response, $context) {
      if (4 != sscanf($request->getQueryString(), '%[^,],%1s,%d,%d', $name, $type, $chapter, $id)) {
        throw new lang::IllegalAccessException('Malformed query string');
      }
      
      if ($album= $this->getAlbumFor($name)) {

        // Add formresult information about the album
        $child= $response->addFormResult(new ('album', NULL, array(
          'name'  => $album->getName(),
          'title' => $album->getTitle(),
          'page'  => $this->getDisplayPageFor($name)
        )));
        
        // Add formresult information depending on type of selected item
        //
        // Add an attribute indicating the next item (if existant):
        // - For highlights, the navigation goes from the first one of
        //   them to the last, wrapping to the first image of the first
        //   chapter if existant, ending there otherwise.
        // - For images contained in chapters, wrap around to the first
        //   image of the next chapter (if existant)
        //
        // Add an attribute indicating the previous item (if existant)
        // - For highlights, the navigation goes from the first one of
        //   them to the last, ending there.
        // - For images contained in chapters, wrap around to the last
        //   image of the previous chapter (if existant)
        $next= $prev= NULL;
        switch ($type) {
          case 'h': {
            $selected= $response->addFormResult(::fromObject($album->highlightAt($id), 'selected'));
            if ($id < $album->numHighlights() - 1) {
              $next= array(
                'type'    => 'h',
                'chapter' => 0,
                'number'  => $id + 1
              );
            } else if (($album->numChapters() > 0) && ($album->chapters[0]->numImages() > 0)) {
              $next= array(
                'type'    => 'i',
                'chapter' => 0,
                'number'  => 0
              );
            }
            if ($id > 0) {
              $prev= array(
                'type'    => 'h',
                'chapter' => 0,
                'number'  => $id- 1
              );
            }
            break;
          }

          case 'i': {
            $selected= $response->addFormResult(::fromObject($album->chapters[$chapter]->imageAt($id), 'selected'));
            if ($id < $album->chapters[$chapter]->numImages() - 1) {
              $next= array(
                'type'    => 'i',
                'chapter' => $chapter,
                'number'  => $id + 1
              );
            } elseif ($chapter < $album->numChapters() - 1) {
              $next= array(
                'type'    => 'i',
                'chapter' => $chapter+ 1,
                'number'  => 0
              );
            }
            if ($id > 0) {
              $prev= array(
                'type'    => 'i',
                'chapter' => $chapter,
                'number'  => $id- 1
              );
            } elseif ($chapter > 0) {
              $prev= array(
                'type'    => 'i',
                'chapter' => $chapter- 1,
                'number'  => $album->chapters[$chapter- 1]->numImages()- 1
              );
            } else {
              $prev= sprintf('h,0,%d', $album->numHighlights()- 1);
            }
            break;
          }
        }

        $selected->setAttribute('type', $type);
        $selected->setAttribute('chapter', $chapter);
        $next && $selected->addChild(::fromArray($next, 'next'));
        $prev && $selected->addChild(::fromArray($prev, 'prev'));

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
