<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
    /**
     * Wrapper for MethodNotImplementedException
     *
     * This exception indicates a certain class method is not
     * implemented.
     */
    class MethodNotImplementedException extends Exception {
        var
            $method= '';
            
        /**
         * Constructor
         *
         * @access  public
         * @param   string message
         * @param   string method
         * @see     lang.Exception#construct
         */
        function __construct($message, $method) {
            $this->method= $method;
            parent::__construct($message);
        }
        
        /**
         * Return stacktrace
         *
         * @return  string stacktrace
         */
        function getStackTrace() {
            return parent::getStackTrace()."\n  [method: {$this->method}]\n";
        }
    }
?>
