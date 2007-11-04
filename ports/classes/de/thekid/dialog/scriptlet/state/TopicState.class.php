<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('de.thekid.dialog.scriptlet.AbstractDialogState');

  /**
   * Handles /xml/topic
   *
   * @purpose  State
   */
  class TopicState extends AbstractDialogState {
    protected
      $nodeHandlers = array();

    /**
     * Constructor
     *
     */
    public function __construct() {
      for ($c= $this->getClass(), $m= $c->getMethods(), $i= 0, $s= sizeof($m); $i < $s; $i++) {
        $m[$i]->hasAnnotation('handles') && (
          $this->nodeHandlers[$m[$i]->getAnnotation('handles')]= $m[$i]
        );
      }
    }

    /**
     * Handler for albums
     *
     * @param   de.thekid.dialog.Album album
     * @return  xml.Node node
     */
    #[@handles('de.thekid.dialog.Album')]
    public function albumNode($album) {
      $child= new Node('entry', NULL, array(
        'name'          => $album->getName(),
        'title'         => $album->getTitle(),
        'num_images'    => $album->numImages(),
        'num_chapters'  => $album->numChapters()
      ));
      $child->addChild(Node::fromObject($album->createdAt, 'created'));
      return $child;
    }

    /**
     * Handler for updates
     *
     * @param   de.thekid.dialog.Update update
     * @return  xml.Node node
     */
    #[@handles('de.thekid.dialog.Update')]
    public function updateNode($update) {
      $child= new Node('entry', NULL, array(
        'album'         => $update->getAlbumName(),
        'title'         => $update->getTitle()
      ));
      $child->addChild(Node::fromObject($update->date, 'created'));
      return $child;
    }

    /**
     * Handler for single shots
     *
     * @param   de.thekid.dialog.SingleShot shot
     * @return  xml.Node node
     */
    #[@handles('de.thekid.dialog.SingleShot')]
    public function shotNode($shot) {
      $child= new Node('entry', NULL, array(
        'name'      => $shot->getName(),
        'title'     => $shot->getTitle()
      ));
      $child->addChild(Node::fromObject($shot->date, 'created'));
      return $child;
    }

    /**
     * Handler for entry collections
     *
     * @param   de.thekid.dialog.EntryCollection collection
     * @return  xml.Node node
     */
    #[@handles('de.thekid.dialog.EntryCollection')]
    public function collectionNode($collection) {
      $node= new Node('entry', NULL, array(
        'name'          => $collection->getName(),
        'title'         => $collection->getTitle(),
        'num_entries'   => $collection->numEntries()
      ));
      $node->addChild(Node::fromObject($collection->created, 'created'));
      return $node;
    }

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     * @param   scriptlet.xml.workflow.Context context
     */
    public function process($request, $response, $context) {
      $name= $request->getQueryString();
      $topic= $this->getEntryFor('topics/'.$name);

      $t= $response->addFormResult(new Node('topic', NULL, array(
        'name'       => $topic->getName(),
        'title'      => $topic->getTitle()
      )));
      $t->addChild(Node::fromObject($topic->getCreatedAt(), 'created'));

      // Add origins
      $o= $t->addChild(new Node('origins'));
      foreach ($topic->origins() as $name) {
        $entry= $this->getEntryFor($name);
        if (!isset($this->nodeHandlers[$entry->getClassName()])) {
          throw new FormatException('Index contains unknown element "'.$entry->getClassName().'"');
        }

        if ($child= $this->nodeHandlers[$entry->getClassName()]->invoke($this, array($entry))) {
          $e= $o->addChild($child);
          $e->setAttribute('type', $entry->getClassName());
          
          foreach ($topic->imagesFrom($name) as $image) {
            $i= $e->addChild(new Node('image', NULL, array(
              'name'          => $image->getName(),
              'origin-name'   => $name,
              'origin-class'  => $entry->getClassName()
            )));
            
            foreach ($entry->imageUrn($image->getName()) as $key => $value) {
              $i->setAttribute('origin-'.$key, $value);
            }
          }
        }
      }
    }
  }
?>
