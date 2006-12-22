<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Rule');

  /**
   * Indicates something was renamed in skeleton
   *
   * @purpose  Rule implementation
   */
  class RenamedRule extends Rule {
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
      return new RuleApplyResult(TRUE, $c);
    }

    /**
     * Creates a string representation of this rule
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return 'renamed to '.$this->new;
    }
  }
?>
