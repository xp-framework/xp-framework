<?php
/*
 *
 * $Id: DiaUMLClass.class.php 8894 2006-12-19 11:31:53Z kiesel $
 */

  namespace org::dia;

  ::uses(
    'org.dia.DiaObject',
    'org.dia.DiaUMLAttribute',
    'org.dia.DiaUMLMethod',
    'org.dia.DiaUMLMethodParameter'
  );

  /**
   * Represents an UML Class shape of a DIAgramm
   *
   * Has annotations which allows automatic instantiation with a given $ClassDoc
   * (XPClass instance)
   *
   * Implements the methods for getting/setting the following object 'attributes':
   * <ul>
   *  <li>name</li> overwrites DiaCompound
   *  <li>stereotype</li> overwrites DiaObject
   *  <li>comment</li>
   *  <li>abstract</li>
   *  <li>suppress_attributes</li>
   *  <li>suppress_operations</li>
   *  <li>visible_attributes</li>
   *  <li>visible_operations</li>
   *  <li>visible_comments</li>
   *  <li>wrap_operations</li>
   *  <li>wrap_after_char</li>
   *  <li>comment_line_length</li>
   *  <li>comment_tagging</li>
   *  <li>normal_font</li>
   *  <li>abstract_font</li>
   *  <li>polymorphic_font</li>
   *  <li>classname_font</li>
   *  <li>abstract_classname_font</li>
   *  <li>comment_font</li>
   *  <li>normal_font_height</li>
   *  <li>abstract_font_height</li>
   *  <li>polymorphic_font_height</li>
   *  <li>classname_font_height</li>
   *  <li>abstract_classname_font_height</li>
   *  <li>comment_font_height</li>
   *  <li>template</li>
   *  <li>attributes</li>
   *  <li>operations</li>
   *  <li>templates</li>
   * </ul>
   *
   * @see   xp://org.dia.DiaDiagram
   * 
   *
   */
  class DiaUMLClass extends DiaObject {

    public
      $int_color= '#88f0ff',   // color of interfaces (blue)
      $exc_color= '#7ded9d',   // color of exceptions (green)
      $err_color= '#ff9f9f';   // color of errors? (red)
      // color for XP classes: #d8e5e5 (gray)
      // color for ??? classes: #ffff9f (yellow)

    /**
     * Constructor
     *
     */
    public function __construct() {
      parent::__construct('UML - Class', 1);

      // always initialize
      $this->initialize();
    }

    /**
     * Initialize this UMLClass object with default values
     *
     */
    public function initialize() {
      // add default values
      $this->setName('__noname__');
      $this->setStereotype(NULL);
      $this->setComment(NULL);

      // add essential nodes
      $this->set('attributes', new DiaAttribute('attributes'));
      $this->set('operations', new DiaAttribute('operations'));
      $this->set('templates', new DiaAttribute('templates'));

      // default flags
      $this->setAbstract(FALSE);
      $this->setBoolean('visible_attributes', FALSE);
      $this->setBoolean('visible_operations', FALSE);
      $this->setBoolean('visible_comments', FALSE);
      $this->setBoolean('suppress_attributes', FALSE);
      $this->setBoolean('suppress_operations', FALSE);
      $this->setBoolean('wrap_operations', FALSE);
      $this->setInt('wrap_after_char', 40);
      $this->setInt('comment_line_length', 40);
      $this->setBoolean('comment_tagging', FALSE);

      // positioning information defaults
      $this->setPosition(array(0, 0));
      $this->setBoundingBox(array(array(0, 0), array(1, 1)));
      $this->setElementCorner(array(0, 0));
      $this->setElementWidth(0.0);
      $this->setElementHeight(0.0);
 
      // default colors and fonts
      $this->setColor('line_color', '#000000');
      $this->setColor('fill_color', '#FFFFFF');
      $this->setColor('text_color', '#000000');
      $this->setFont('normal_font', array(
        'family'  => 'monospace',
        'style'   => 0,
        'name'    => 'Courier'
      ));
      $this->setFont('abstract_font', array(
        'family'  => 'monospace',
        'style'   => 88,
        'name'    => 'Courier-BoldOblique'
      ));
      $this->setFont('polymorphic_font', array(
        'family'  => 'monospace',
        'style'   => 8,
        'name'    => 'Courier-Oblique'
      ));
      $this->setFont('classname_font', array(
        'family'  => 'sans',
        'style'   => 80,
        'name'    => 'Helvetica-Bold'
      ));
      $this->setFont('abstract_classname_font', array(
        'family'  => 'sans',
        'style'   => 88,
        'name'    => 'Helvetica-BoldOblique'
      ));
      $this->setFont('comment_font', array(
        'family'  => 'sans',
        'style'   => 8,
        'name'    => 'Helvetica-Oblique'
      ));

      // default font sizes
      $this->setReal('normal_font_height', 0.8);
      $this->setReal('abstract_font_height', 0.8);
      $this->setReal('polymorphic_font_height', 0.8);
      $this->setReal('classname_font_height', 1);
      $this->setReal('abstract_classname_font_height', 1);
      $this->setReal('comment_font_height', 1);
    }

    /** 
     * Returns the name of the class
     *
     * @return  string
     */
    public function getName() {
      return $this->getChildValue('name');
    }

    /**
     * Sets the name of the UML class (overwrites method from DiaCompound due
     * to additional annotation!)
     *
     * @param   string name
     */
    #[
    # @fromClass(type = 'string', eval = '$ClassDoc->qualifiedName()'),
    # @fromDia(xpath= 'dia:attribute[@name="name"]/dia:string', value= 'string')
    #]
    public function setName($name) {
      $this->setString('name', $name);
    }

    /**
     * Sets the stereotype of the UML class
     *
     * @param   string stereotype
     */
    #[
    # @fromClass(type = 'string', eval = '$ClassDoc->classType()'),
    # @fromDia(xpath= 'dia:attribute[@name="stereotype"]/dia:string', value= 'string')
    #]
    public function setStereotype($stereotype) {
      switch ($stereotype) {
        case ORDINARY_CLASS: 
          return; // no stereotype
        case EXCEPTION_CLASS:
          // exceptions are gree
          $this->set('fill_color', new DiaAttribute('fill_color', $this->exc_color, 'color'));
          $type= 'Exception';
          break;
        case ERROR_CLASS:
          // errors are red
          $this->set('fill_color', new DiaAttribute('fill_color', $this->err_color, 'color'));
          $type= 'Error';
          break;
        case INTERFACE_CLASS: 
          // interfaces are blue
          $this->set('fill_color', new DiaAttribute('fill_color', $this->int_color, 'color'));
          $type= 'Interface';
          break;
        default:
          $type= $stereotype;
          //return throw(new IllegalArgumentException("Unknown class type: '$type'!"));
      }
      $this->setString('stereotype', $type);
    }

    /**
     * Sets the comment of the UML class
     *
     * @param   string comment
     */
    #[
    # @fromClass(type = 'string', eval = '$ClassDoc->commentText()'),
    # @fromDia(xpath= 'dia:attribute[@name="comment"]/dia:string', value= 'string')
    #]
    public function setComment($comment) {
      $this->setString('comment', $comment);
    }

    /**
     * Sets the 'abstract' attribute of the UML class
     *
     * Evaluates 'in_array(\'abstract\', $ClassDoc->tags(\'model\'))'
     * ($ClassDoc->parseDetail('tags') && isset($ClassDoc->tags['model'][0]) && $ClassDoc->tags['model'][0]->text() === 'abstract'); 
     *
     * @param   bool abstract
     */
    #[
    # @fromClass(type = 'bool', eval = 
    #  '$ClassDoc->parseDetail("tags") && isset($ClassDoc->tags["model"][0]) && $ClassDoc->tags["model"][0]->text() === "abstract"'
    # ),
    # @fromDia(xpath= 'dia:attribute[@name="abstract"]/dia:boolean/@val', value= 'boolean')
    #]
    public function setAbstract($abstract) {
      $this->setBoolean('abstract', $abstract);
    }

    /**
     * Sets the 'suppress_attributes' attribute of the UML class
     *
     * @param   bool suppress
     */
    #[@fromDia(xpath= 'dia:attribute[@name="suppress_attributes"]/dia:boolean/@val', value= 'boolean')]
    public function suppressAttributes($suppress) {
      $this->setBoolean('suppress_attributes', $suppress);
    }

    /**
     * Sets the 'suppress_operations' attribute of the UML class
     *
     * @param   bool suppress
     */
    #[@fromDia(xpath= 'dia:attribute[@name="suppress_operations"]/dia:boolean/@val', value= 'boolean')]
    public function suppressOperations($suppress) {
      $this->setBoolean('suppress_operations', $suppress);
    }

    /**
     * Sets the 'visible_attributes' attribute of the UML class
     *
     * @param   bool visible
     */
    #[@fromDia(xpath= 'dia:attribute[@name="visible_attributes"]/dia:boolean/@val', value= 'boolean')]
    public function showAttributes($visible) {
      $this->setBoolean('visible_attributes', $visible);
    }

    /**
     * Sets the 'visible_operations' attribute of the UML class
     *
     * @param   bool visible
     */
    #[@fromDia(xpath= 'dia:attribute[@name="visible_operations"]/dia:boolean/@val', value= 'boolean')]
    public function showOperations($visible) {
      $this->setBoolean('visible_operations', $visible);
    }

    /**
     * Sets the 'visible_comments' attribute of the UML class
     *
     * @param   bool visible
     */
    #[@fromDia(xpath= 'dia:attribute[@name="visible_comments"]/dia:boolean/@val', value= 'boolean')]
    public function showComments($visible) {
      $this->setBoolean('visible_comments', $visible);
    }

    /**
     * Sets the 'wrap_operatoins' attribute of the UML class
     *
     * @param   bool wrap
     */
    #[@fromDia(xpath= 'dia:attribute[@name="wrap_operations"]/dia:boolean/@val', value= 'boolean')]
    public function wrapOperations($wrap) {
      $this->setBoolean('wrap_operations', $wrap);
    }

    /**
     * Sets the 'wrap_after_char' attribute of the UML class
     *
     * @param   int char
     */
    #[@fromDia(xpath= 'dia:attribute[@name="wrap_after_char"]/dia:int/@val', value= 'int')]
    public function wrapAfterChar($char) {
      $this->setInt('wrap_after_char', $char);
    }

    /**
     * Sets the 'comment_line_length' attribute of the UML class
     *
     * @param   int length
     */
    #[@fromDia(xpath= 'dia:attribute[@name="comment_line_length"]/dia:int/@val', value= 'int')]
    public function setCommentLineLength($length) {
      $this->setInt('comment_line_length', $length);
    }

    /**
     * Sets the 'comment_tagging' attribute of the UML class
     *
     * @param   bool tagging
     */
    #[@fromDia(xpath= 'dia:attribute[@name="comment_tagging"]/dia:boolean/@val', value= 'boolean')]
    public function setCommentTagging($tagging) {
      $this->setBoolean('comment_tagging', $tagging);
    }

    /**
     * Sets the 'normal_font' attribute of the UML class
     *
     * @param   array font
     */
    #[@fromDia(xpath= 'dia:attribute[@name="normal_font"]/dia:font', value= 'font')]
    public function setNormalFont($font) {
      $this->setFont('normal_font', $font);
    }

    /**
     * Sets the 'abstract_font' attribute of the UML class
     *
     * @param   array font
     */
    #[@fromDia(xpath= 'dia:attribute[@name="abstract_font"]/dia:font', value= 'font')]
    public function setAbstractFont($font) {
      $this->setFont('abstract_font', $font);
    }

    /**
     * Sets the 'polymorphic_font' attribute of the UML class
     *
     * @param   array font
     */
    #[@fromDia(xpath= 'dia:attribute[@name="polymorphic_font"]/dia:font', value= 'font')]
    public function setPolymorphicFont($font) {
      $this->setFont('polymorphic_font', $font);
    }

    /**
     * Sets the 'classname_font' attribute of the UML class
     *
     * @param   array font
     */
    #[@fromDia(xpath= 'dia:attribute[@name="classname_font"]/dia:font', value= 'font')]
    public function setClassnameFont($font) {
      $this->setFont('classname_font', $font);
    }

    /**
     * Sets the 'abstract_classname_font' attribute of the UML class
     *
     * @param   array font
     */
    #[@fromDia(xpath= 'dia:attribute[@name="abstract_classname_font"]/dia:font', value= 'font')]
    public function setAbstractClassnameFont($font) {
      $this->setFont('abstract_classname_font', $font);
    }

    /**
     * Sets the 'comment_font' attribute of the UML class
     *
     * @param   array font
     */
    #[@fromDia(xpath= 'dia:attribute[@name="comment_font"]/dia:font', value= 'font')]
    public function setCommentFont($font) {
      $this->setFont('comment_font', $font);
    }

    /**
     * Sets the 'normal_font_height' attribute of the UML Class
     *
     * @param   float height
     */
    #[@fromDia(xpath= 'dia:attribute[@name="normal_font_height"]/dia:real/@val', value= 'real')]
    public function setNormalFontHeight($height) {
      $this->setReal('normal_font_height', $height);
    }

    /**
     * Sets the 'abstract_font_height' attribute of the UML Class
     *
     * @param   float height
     */
    #[@fromDia(xpath= 'dia:attribute[@name="abstract_font_height"]/dia:real/@val', value= 'real')]
    public function setAbstractFontHeight($height) {
      $this->setReal('abstract_font_height', $height);
    }

    /**
     * Sets the 'polymorphic_font_height' attribute of the UML Class
     *
     * @param   float height
     */
    #[@fromDia(xpath= 'dia:attribute[@name="polymorphic_font_height"]/dia:real/@val', value= 'real')]
    public function setPolymorphicFontHeight($height) {
      $this->setReal('polymorphic_font_height', $height);
    }

    /**
     * Sets the 'classname_font_height' attribute of the UML Class
     *
     * @param   float height
     */
    #[@fromDia(xpath= 'dia:attribute[@name="classname_font_height"]/dia:real/@val', value= 'real')]
    public function setClassnameFontHeight($height) {
      $this->setReal('classname_font_height', $height);
    }

    /**
     * Sets the 'abstract_classname_font_height' attribute of the UML Class
     *
     * @param   float height
     */
    #[@fromDia(xpath= 'dia:attribute[@name="abstract_classname_font_height"]/dia:real/@val', value= 'real')]
    public function setAbstractClassnameFontHeight($height) {
      $this->setReal('abstract_classname_font_height', $height);
    }

    /**
     * Sets the 'comment_font_height' attribute of the UML Class
     *
     * @param   float height
     */
    #[@fromDia(xpath= 'dia:attribute[@name="comment_font_height"]/dia:real/@val', value= 'real')]
    public function setCommentFontHeight($height) {
      $this->setReal('comment_font_height', $height);
    }

    /**
     * Sets the 'template' attribute of the UML class
     *
     * @param   bool template
     */
    #[@fromDia(xpath= 'dia:attribute[@name="template"]/dia:boolean/@val', value= 'boolean')]
    public function setTemplate($template) {
      $this->setBoolean('template', $template);
    }

    /************************* *************************/

    /**
     * Adds an UML attribute to the UML class
     *
     * @param   &org.dia.DiaUMLAttribute Attribute
     */
    // TODO? fromClass(type= 'attribute', class= 'org.dia.DiaUMLAttribute')
    #[@fromDia(xpath= 'dia:attribute[@name="attributes"]/*', class= 'org.dia.DiaUMLAttribute')]
    public function addUMLAttribute($Attribute) {
      $Attributes= $this->getChild('attributes');
      $Attributes->set($Attribute->getName(), $Attribute);
    }

    /**
     * Adds an UML method to the UML class
     * 
     * @param   &org.dia.DiaUMLMethod Method
     */
    #[@fromDia(xpath= 'dia:attribute[@name="operations"]/*', class= 'org.dia.DiaUMLMethod')]
    public function addUMLMethod($Method) {
      $Operations= $this->getChild('operations');
      $Operations->set($Method->getName(), $Method);
    }

    /**
     * Adds an UML formal parameter to the UML class
     *
     * @param   &org.dia.DiaUMLFormalParameter Parameter
     */
    #[@fromDia(xpath= 'dia:attribute[@name="templates"]/*', class= 'org.dia.DiaUMLFormalParameter')]
    public function addTemplate($Parameter) {
      $Templates= $this->getChild('templates');
      $Templates->set($Parameter->getName(), $Parameter);
    }

    /**
     * Adds a 'dia:childnode' link to the parent object
     *
     * @param   &org.dia.DiaChildnode Childnode
     */
    #[@fromDia(xpath= 'dia:childnode', class= 'org.dia.DiaChildnode')]
    public function addParentLink($Childnode) {
      $this->set('childnode', $Childnode);
    }

    /**
     * Adds an attribute to the UML class
     *
     * $field= array($name => $value)
     *
     * @param   array field
     */
    #[@fromClass(type = 'attribute')]
    public function addAttribute($field) {
      $Attributes= $this->getChild('attributes');

      // create new UMLAttribute
      list($name, $value)= each($field);
      $Attrib= new DiaUMLAttribute();
      $Attrib->setName($name);

      // determine type if possible
      if (isset($value)) {
        $type= ::xp::typeOf(eval("return $value;"));
      } else {
        $type= NULL;
        $value= 'NULL';
      }
      $Attrib->setValue($value);
      if (isset($type)) $Attrib->setType($type);

      // public attribute?
      if (0 == strncmp('_', $name, 1)) {
        $Attrib->setVisibility(2); // default: 0
      }

      // add to attributes node
      $Attributes->set($Attrib->getName(), $Attrib);
    }

    /**
     * Adds a method to the UML class
     * 
     * @param   &text.doclet.MethodDoc Method
     */
    #[@fromClass(type = 'method')]
    public function addMethod($Method) {
      $Operations= $this->getChild('operations');

      // create new UMLMethod
      $Oper= new DiaUMLMethod();
      $Oper->setName($Method->name());

      // check @return tag
      $return_tags= $Method->tags('return');
      if (!empty($return_tags)) {
        $type= $return_tags[0]->type;
      } else {
        $type= NULL;
      }
      $Oper->setType($type);

      // public method?
      if (0 == strncmp('_', $Method->name(), 1)) {
        $Oper->setVisibility(2);
      }
      $Oper->setComment($Method->commentText());

      // check @model tags
      $model_tags= $Method->tags('model');
      if (!empty($model_tags)) {
        foreach (array_keys($model_tags) as $key) {
          switch ($model_tags[$key]->text()) {
            case 'abstract': 
              $Oper->setAbstract(TRUE); break;
            case 'static':
              $Oper->setClassScope(TRUE); break;
          }
        }
      }

      // TODO? stereotype, inheritance_type, class_scope... (@access?)

      // get parameters 'attribute'
      $Params= $Oper->getChild('parameters');

      // loop over arguments
      foreach (array_keys($Method->arguments) as $name) {
        $value= $Method->arguments[$name]; // always string!
        if (isset($value)) {
          $evalue= eval("return $value;");
          if (isset($evalue)) $type= ::xp::typeOf($evalue);
        } else {
          $type= NULL;
          $value= NULL;
        }

        // create parameter 'composite'
        $Param= new DiaUMLMethodParameter();
        $Param->setName($name);
        $Param->setValue($value);
        if (isset($type)) $Param->setType($type);

        // add to parameters node
        $Params->set($Param->getName(), $Param);
      }

      // add to operations node
      $Operations->set($Oper->getName(), $Oper);
    }
  }
?>
