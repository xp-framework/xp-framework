<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('de.thekid.dialog.scriptlet.AbstractDialogState');

  /**
   * Handles /xml/bydate
   *
   * @purpose  State
   */
  class BydateState extends AbstractDialogState {
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
      $child->addChild(new Node('description', new PCData($album->getDescription())));
      $child->addChild(new Node('highlight', $album->highlightAt(0)->getName()));
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
      $child->addChild(new Node('description', new PCData($update->getDescription())));
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
      $child->addChild(new Node('description', new PCData($shot->getDescription())));
      $child->addChild(new Node('highlight', $shot->getFileName()));
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
      $first= $collection->entryAt(0);
      $node= new Node('entry', NULL, array(
        'name'          => $collection->getName(),
        'first'         => $first->getName(),
        'title'         => $collection->getTitle(),
        'num_entries'   => $collection->numEntries()
      ));
      $node->addChild(Node::fromObject($collection->created, 'created'));
      $node->addChild(new Node('description', new PCData($collection->getDescription())));
      $node->addChild(new Node('highlight', $first->highlightAt(0)->getName()));
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
      sscanf($request->getQueryString(), '%4d', $year);

      // Calculate year from newest item
      if (NULL === $year) {
        $newest= $this->getIndexPage(0);
        sscanf(key($newest['entries']), '%4d', $year);
      }

      // Add paging information
      $years= $this->getIndexYears();
      $response->addFormResult(new Node('years', NULL, array(
        'current' => $year,
        'min'     => min($years),
        'max'     => max($years)
      )));

      $index= $this->getIndexYear($year);
      $node= $response->addFormResult(new Node('entries'));
      $nodes= array();
      foreach ($index as $month => $entries) {
        $nodes[$m]= $node->addChild(new Node('month', NULL, array(
          'num'  => $month,
          'year' => $year
        )));

        foreach ($entries as $name) {
          $entry= $this->getEntryFor($name);
          if (!isset($this->nodeHandlers[$entry->getClassName()])) {
            throw new FormatException('Index contains unknown element "'.$entry->getClassName().'"');
          }

          if ($child= $this->nodeHandlers[$entry->getClassName()]->invoke($this, array($entry))) {
            $nodes[$m]->addChild($child)->setAttribute('type', $entry->getClassName());
          }
        }
      }
    }
  }
?>
