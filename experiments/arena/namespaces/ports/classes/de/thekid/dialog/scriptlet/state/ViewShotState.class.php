<?php
/* This class is part of the XP framework
 *
 * $Id: ViewShotState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::dialog::scriptlet::state;

  ::uses('de.thekid.dialog.scriptlet.AbstractDialogState');

  /**
   * Handles /xml/shot/view
   *
   * @purpose  State
   */
  class ViewShotState extends de::thekid::dialog::scriptlet::AbstractDialogState {

    /**
     * Process this state.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     * @param   &scriptlet.xml.workflow.Context context
     */
    public function process($request, $response, $context) {
      static $modes= array(
        0 => 'color',
        1 => 'gray'
      );

      if (
        (2 != sscanf($request->getQueryString(), '%[^,],%d', $name, $mode)) ||
        !isset($modes[$mode])
      ) {
        throw(new lang::IllegalAccessException('Malformed query string'));
      }

      if ($shot= $this->getEntryFor($name, 'de.thekid.dialog.SingleShot')) {
        $s= $response->addFormResult(::fromObject($shot, 'selected'));
        $s->setAttribute('mode', $modes[$mode]);
        $s->setAttribute('page', $this->getDisplayPageFor($name));
      }
    }
  }
?>
