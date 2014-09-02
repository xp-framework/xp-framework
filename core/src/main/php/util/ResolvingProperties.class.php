<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  
  uses(
    'lang.Object',
    'lang.ElementNotFoundException',
    'lang.FormatException',
    'lang.XPException',
    'util.PropertyManager',
    'util.PropertyDecorator'
  );

  /**
   * Decorator for util.Properties which allows the usage of special tokens in string values:
   *
   * For example
   * <pre>
   * [section]
   * key1=${prop.properties.section.key}
   * key2=${prop.properties.section.key|default}
   * key3=${env.envname}
   * </pre>
   *
   * Available tokens:
   * * env
   *   Returns the content of an environment variable.
   * * prop
   *   Returns the content of another property object
   *
   * @see   util.Properties
   * @test  xp://net.xp_framework.unittest.util.ResolvingPropertiesTest
   */
  class ResolvingProperties extends PropertyDecorator  {

    protected $propertyManager;

    /**
     * @param PropertyAccess $decoratedProperties
     * @param PropertyManager $propertyManager Allows to inject a specific property manager
     */
    public function __construct(
      PropertyAccess $decoratedProperties,
      PropertyManager $propertyManager= null
    ) {
      $this->properties= $decoratedProperties;
      $this->propertyManager= $propertyManager ?: PropertyManager::getInstance();
    }

    /**
     * Read string value
     *
     * @param   string $section
     * @param   string $key
     * @param   mixed $default default NULL
     * @return  string
     */
    public function readString($section, $key, $default= NULL) {
      $ret= $this->properties->readString($section, $key, $default);

      if ($ret != NULL && strpos($ret, '${') !== false) {
        $ret= preg_replace_callback(
          '/\$\{([^.}]*)\.([^}|]*)(?:\|([^}]*))?\}/',
          array($this, 'replaceToken'),
          $ret
        );
      }

      return $ret;
    }

    /**
     * Callback function for token replace regex
     *
     * @param   string[] $match
     * @return  string
     * @throws  lang.FormatException
     */
    private function replaceToken($match) {
      $arguments= $match[2];
      $default= isset($match[3]) ? $match[3] : NULL;
      $replacement= '';
      switch ($match[1]) {
        case 'prop':
          $replacement= $this->processPropToken($arguments, $default);
          break;
        case 'env':
          $replacement= $this->processEnvToken($arguments, $default);
          break;
        default:
          throw new FormatException('Unknown placeholder "' . $match[1] . '"');
      }
      return $replacement;
    }

    /**
     * Processes the "prop" token
     *
     * @param   string $arguments
     * @param   string $default
     * @return  string
     * @throws  lang.ElementNotFoundException
     * @throws  lang.FormatException
     */
    private function processPropToken($arguments, $default) {
      $value= '';
      $args= explode('.', $arguments);
      if (count($args) != 3) {
        throw new FormatException('Invalid arguments "' . $arguments . '"');
      }
      if ($this->propertyManager->hasProperties($args[0])) {
        $properties= $this->propertyManager->getProperties($args[0]);
        // Avoid endless loops by limiting the token parsing depth
        if ($properties instanceof ResolvingProperties) {
          $properties= $properties->getDecoratedProperties();
        }
        $value= $properties->readString($args[1], $args[2], NULL);
        if ($value === NULL) {
          if ($default !== NULL) {
            $value= $default;
          } else {
            throw new ElementNotFoundException(
              'Can\'t find string "' . $args[2] . '" in section "' . $args[1]  . '"'
            );
          }
        }
      } else {
        if ($default !== NULL) {
          $value= $default;
        } else {
          throw new ElementNotFoundException(
            'Can\'t find properties "' . $args[0] . '"'
          );
        }
      }
      return $value;
    }

    /**
     * Processes the "env" token
     *
     * @param   string $arguments
     * @param   string $default
     * @return  string
     * @throws  lang.ElementNotFoundException
     */
    private function processEnvToken($arguments, $default) {
      $value= getenv($arguments);
      if ($value === false) {
        if ($default !== NULL) {
          $value= $default;
        } else {
          throw new ElementNotFoundException(
            'Environment variable "' . $arguments . '" doesn\'t exists'
          );
        }
      }
      return $value;
    }

  }