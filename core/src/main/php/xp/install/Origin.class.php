<?php
  namespace xp\install;

  /**
   * The origin of a module
   */
  interface Origin {

    /**
     * Fetches this origin into a given target folder
     *
     * @param  io.Folder $target
     */
    public function fetchInto(\io\Folder $target);
  }
?>