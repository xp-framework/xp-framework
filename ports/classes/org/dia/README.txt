How to use 'xp://org.dia.*'
===========================
@author: Pascal Sick (muc@sicknet.de)

CONTENTS:
=========
* urgent TODOs
* HOWTO
* FILES
* TODOs
* FUTURE
* The updating process (concept)
* UML2 support for dia (thoughts)

1) urgent TODOs:
----------------
* if given lots of classes, some don't get recursed or only used as dependency
* and not as parent class although generalization should take precedence!
==> improve recusion! done? check!
* generate UNIQUE ids for all _children elements i.e. $element_name+$element_id?

2a) HOWTO: create DIAgrams:
---------------------------
HINT: at this stage of development, the easiest way of 'generating' a diagram
is to create a new diagram by hand which includes all desired classes with fully
qualified classname. After that you can use 'update_diagram.php' (see 2b) to
automatically add all attributes and methods to the classes. 


TODO: New way of generating diagrams:
1) collect Classes, Dependencies, Generalizations etc...
2) Generate and add (empty) DiaUML* objects to a new DiaDiagram
3) run UpdateVisitor to update all objects in the DiaDiagram (adds attributes and methods)

part1: (ObjectCollector)
- collector: collect classes, dependencies, generalizations, implementations etc. 
  -> can be controlled via options (recursion, dependencies, ...)
  => generates a generic list of "objects" and their connections
part2: (
- given a list of "objects" generate appropriate DiaUML* objects which are
  already connected to each other and add them to the given DiaDiagram
  => returns DiaDiagram with added objects
part3:
- given a DiaDiagram and some options, UpdateVisitor updates the appropriate
  objects in the DiaDiagram
  => returns updated DiaDiagram

2b) HOWTO: update DIAgrams:
---------------------------
> php update_diagram.php DIAGRAM.DIA
=> uses UpdateVisitor to update all classes found in the diagram

OBSOLET?:
> php update.php --diagram=test.dia --classes=lang.Object,util.Date

3) FILES:
---------
DiaMarshaller   : create DiaDiagram from a list of classnames
DiaUnmarshaller : parse XML DiaDiagram and create XP class-tree starting at
                  DiaDiagram which can be traversed by a Visitor

* interfaces:
DiaComponent : Interface class for DiaElement, DiaCompound, DiaComposite and DiaObject

* base classes:
DiaElement : Base class for simple elements like 'string', 'int', 'font', 'color', ...
DiaCompound : Base class for compound elements, containing further DiaComponent elements

* compound elements
DiaDiagram : representation of a DIAgram
DiaData : representation of a DIAgram 'diagramdata' element
DiaLayer : representation of a DIAgram 'layer' element
DiaGrid : representation of the 'grid' element
DiaGuides : ... 'guides'
DiaText : ... 'text'
DiaPaper : ... 'paper'
DiaRole : ... 'role'
DiaComposite : representation of a DIAgram 'composite' element
DiaAttribute : represenation of a DIAgram 'attribute' element
DiaObject : representation of a DIAgram 'object' element

* simple elements:
DiaBoolean
DiaInt
DiaReal
DiaString
DiaEnum
DiaColor
DiaFont
DiaPoint
DiaRectangle

* specific elements:
DiaUMLClass : representation of an UML class in DIA (extends DiaObject)
DiaUMLConnection : base class for UML connection types (dependency,
  realization, implementation, association, ...)
DiaUML* : represent what their name sais ;)

* Doclet (obsolete?)
DiaDoclet : hands the given classes over to DiaMarshaller to generate a DiaDiagram

* Visitors
UpdateVisitor : takes a DiaDiagram and updates the given (or all) classes in the diagram
FUTURE: LayoutVisitor : tries to do some layouting? Better do it by hand...

4) TODOs:
---------
* implement missing DIA (Standard) classes: (see diagrams/dia_shapes.dia)
  DiaBox, DiaEllipse, DiaPolygon, DiaBeziergon, DiaLine, DiaArc, DiaZigZagLine,
  DiaPolyLine, DiaBezierLine, DiaImage
* implement missing UML classes: (see diagrams/uml_shapes.dia)
  UMLConstraint, UMLSmallPackage, UMLActor, UMLUseCase, UMLLifeLine, UMLObject,
  UMLMessage, UMLComponent, UMLComponentFeature, UMLNode, UMLClassIcon, UMLStateTerm, 
  UMLState, UMLActivity, UMLBranch, UMLFork, UMLTransition
* update dependencies and such...

5) FUTURE:
----------
* yet another REDESIGN: separate 'UML' notation stuff from 'dia' specific classes
=> have XP classes which represent a UML diagram - independent of 'dia'
=> have an 'Visitor' which goes through the UML diagram structure and creates a 'dia' diagram from it?
=> OR just use annotations? (done)

* database diagrams:
- use XML generated for 'DataSet' classes to generate DiaUMLClass objects?
-OR-
- use ClassDoc of 'DataSet' classes to generate the diagram, just like we do now? 
  (NO, because: I think they have too many differences in interpreting 'class'
  methods and attributes... i.e. UML ERD vs. UML Classdiagram)

6) The updating process: (concept)
------------------------
main priciples:
* once a diagram is created it is useless unless up-to-date!
* people tend to not have the time (tm) to update diagrams, instead they work 24/7 on the source code
=> we assume here, that diagram are always more outdated than the source code
=> it would be nice, if manually layouted diagrams would reflect the current (or versioned) source code
* a class can only be uniquely identified in xml.dia by its classname, therefore classnames should never change
* dependencies, implemenations and generalizations are more easily updated by hand! check them anyway?

update.php --diagram=FILE --classes=my.class.Name[,another.Class,...]:
-----------
1. does the file exist? 
  yes: update (parse the existing XML diagram)
  no: generate new diagram containing the given classes
2. does the given classname exist in diagram? 
  no: add class to diagram (TODO)
  yes: loop over class attributes and methods:
    - update existing attributes and methods (with parameters)
    - add missing attributes and methods (with parameters)
    - delete additional attributes and methods
3. write the diagram to file

HINT: of course one can use diagrams to generate code, but I have experienced, 
that this is done only at the very beginning of a project. So I won't
work on anything updating code from diagrams.

7) UMLv2 for dia? (thoughts)
-----------------
* research: UML2 support in dia?
* inform: how get dia objects/shapes described/implemented?
=> maybe it is possible to create UML2 objects/shapes for dia in a generic way
   (description file) and use these description files to generate appropriate
   classes to generate them as XML.

i.e:
define UML-object description
=> generate dia UML shapes
=> generate php|java|... classes representing such a dia shape
