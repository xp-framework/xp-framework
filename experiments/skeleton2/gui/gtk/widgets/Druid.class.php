<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('gui.gtk.GtkGladeDialogWindow');

  /**
   * Druid
   *
   * @purpose Provide a widget for step-by-step
   */
  class Druid extends GtkGladeDialogWindow {

    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct() {
      parent::__construct(dirname(__FILE__).'/druid.glade', 'druid');
    }

  }
?>
