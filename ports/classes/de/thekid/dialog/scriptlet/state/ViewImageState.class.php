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

        // Add formresult information about the album
        $child= &$response->addFormResult(new Node('album', NULL, array(
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
            $selected= &$response->addFormResult(Node::fromObject($album->highlightAt($id), 'selected'));
            if ($id < $album->numHighlights() - 1) {
              $next= sprintf('h,0,%d', $id+ 1);
            } else if (($album->numChapters() > 0) && ($album->chapters[0]->numImages() > 0)) {
              $next= 'i,0,0';
            }
            if ($id > 0) {
              $prev= sprintf('h,0,%d', $id- 1);
            }
            break;
          }

          case 'i': {
            $selected= &$response->addFormResult(Node::fromObject($album->chapters[$chapter]->imageAt($id), 'selected'));
            if ($id < $album->chapters[$chapter]->numImages() - 1) {
              $next= sprintf('i,%d,%d', $chapter, $id+ 1);
            } elseif ($chapter < $album->numChapters() - 1) {
              $next= sprintf('i,%d,0', $chapter+ 1);
            }
            if ($id > 0) {
              $prev= sprintf('i,%d,%d', $chapter, $id- 1);
            } elseif ($chapter > 0) {
              $prev= sprintf('i,%d,%d', $chapter- 1, $album->chapters[$chapter- 1]->numImages()- 1);
            } else {
              $prev= sprintf('h,0,%d', $album->numHighlights()- 1);
            }
            break;
          }
        }

        $selected->setAttribute('type', $type);
        $selected->setAttribute('chapter', $chapter);
        $next && $selected->setAttribute('next', $name.','.$next);
        $prev && $selected->setAttribute('prev', $name.','.$prev);

        // Check if an album is inside a collection
        if (FALSE === ($p= strpos($name, '/'))) return; 

        $parent= &$this->getEntryFor(substr($name, 0, $p));
        $child->setAttribute('page', $this->getDisplayPageFor($parent->getName()));
        $child->addChild(new Node('collection', NULL, array(
          'name'         => $parent->getName(),
          'title'        => $parent->getTitle()
        )));
      }
    }
  }
?>
