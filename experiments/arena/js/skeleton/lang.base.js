{
/* This is the core of the XP javascript framework
 *
 * $Id$ 
 */

  // {{{ proto void uses(string* args)
  //     Makes sure all of the required classes are available
  function uses() {
    for (i= 0; i < arguments.length; i++) {
      if (typeof this[arguments[i]] == 'undefined') {
        throw new Exception(
          'The required class "' + arguments[i] + '" is not available'
        );
      }
    }
  }
  // }}}
 
}
