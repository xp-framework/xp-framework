<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Abstract Person description to be used in the basic user class
   *
   */
    class AbstractPerson extends Object {
        var $name= '',
            $firstname= '',
            $email= '',
            $rights = array();
            
        /**
         * returns true if the person has the given right
         * @access public
         * @params String rightname
         * 
         * TODO: define necessary params to uniquely specify a given right
         */
        function hasRight($rightname) {
            foreach ($this->rights as $right) {
                if ($right == $rightname) {
                    return true;
                }
            }

            return false;
        }

    }
?>
