<?php
/* This class is part of the XP framework
 *
 * $Id: InjectionProvider.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  /**
   * Interface for injection value providers.
   */
  interface InjectionProvider {

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = NULL);
  }
?>