<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Rule');

  /**
   * Indicates something was moved to ports/classes
   *
   * @purpose  Rule implementation
   */
  class MovedRule extends Rule {
    var 
      $new= '';
    
    /**
     * Constructor
     *
     * @access  public
     * @return  string new new package
     */
    function __construct($new) {
      $this->new= $new;
    }

    /**
     * Apply this rule to a given sourcecode 
     *
     * @access  public
     * @param   string package
     * @param   &text.String source
     * @return  &RuleApplyResult
     */
    function applyTo($package, $source) {
      $pattern= '/'.preg_quote($package).'/';
      if (0 != ($c= preg_match($pattern, $source->buffer))) {
        if (FALSE === ($replaced= preg_replace($pattern, $this->new, $source->buffer))) {
          return new RuleApplyResult(FALSE, 0, xp::stringOf(new FormatException('Regex broken')));
        }
        $source->buffer= $replaced;
      }
      return new RuleApplyResult(TRUE, $c, 'Include ports/classes in your include_path!');
    }

    /**
     * Creates a string representation of this rule
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return 'moved to '.$this->new.' in ports/classes';
    }
  }
?>
