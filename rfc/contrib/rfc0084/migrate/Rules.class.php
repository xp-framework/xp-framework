<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'DeprecatedRule',
    'RenamedRule',
    'MovedRule'
  );

  /**
   * Rule definitions
   *
   * @purpose  Rule container
   */
  class Rules extends Object {
  
    /**
     * Returns all rules
     *
     * @model   static
     * @access  public
     * @param   array<string, &Rule> rules
     */
    function allRules() {
      return array(
        'gui.gtk'                 => new MovedRule('org.gnome'),
        'org.json'                => new RenamedRule('webservices.json'),
        'xml.xmlrpc'              => new RenamedRule('webservices.xmlrpc'),
        'xml.soap'                => new RenamedRule('webservices.soap'),
        'xml.wddx'                => new RenamedRule('webservices.wddx'),
        'xml.uddi'                => new RenamedRule('webservices.uddi'),
        'xml.xp'                  => new DeprecatedRule(array('xml.meta')),
        'io.cca'                  => new DeprecatedRule(array('lang.archive')),
        'util.profiling.unittest' => new RenamedRule('unittest'),
        'util.archive'            => new MovedRule('org.gnu.tar'),
        'util.adt'                => new DeprecatedRule(array('util.collections')),
        'util.registry'           => new DeprecatedRule(),
        'util.mp3'                => new MovedRule('de.fraunhofer.mp3'),
        'peer.ajp'                => new MovedRule('org.apache.ajp'),
        'peer.cvsclient'          => new MovedRule('org.cvshome'),
        'text.apidoc'             => new DeprecatedRule(array('text.doclet')),
        'text.translator'         => new MovedRule('net.schweikhardt'),
        'net.planet-xp'           => new DeprecatedRule(),
      );
    }
  }
?>
