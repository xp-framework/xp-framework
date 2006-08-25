How to use 'xp://org.dia.*'
===========================
@author: Pascal Sick (sick@schlund.de)

HOWTO: create DIAgrams:
-----------------------

> php doclet.php --doclet=org.dia.DiaDoclet --verbose --depend --gzipped --recurse=5 --directory=/diagrams --file=test.dia util.Date lang.XPClass [...]

DiaDoclet options:
* --verbose
* --gzipped
* --depend
* --recurse=level
* --directory=$DIR
* --file=$FILE

FILES:
======
DiaMarshaller : create DiaDiagram from a list of classnames
TODO: DiaUnmarshaller : create XP classes from DiaUMLClass as XML

* interfaces:
DiaComponent : Interface class for DiaElement, DiaCompound, DiaComposite and DiaObject

* base classes:
DiaElement : Base class for simple elements like 'string', 'int', 'font', 'color', ...
DiaCompound : Base class for compound elements, containing further DiaComponent elements

* compound elements
DiaDiagram : representation of a DIAgram
DiaData : representation of a DIAgram 'diagramdata' element
DiaLayer : representation of a DIAgram 'layer' element
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

* Doclet
DiaDoclet : hands the given classes over to DiaMarshaller to generate a DiaDiagram

TODOs:
======
* merge generated diagram with the positions from an already (manually) layouted diagram (tool)
* update a diagram?
* adjacent placement of objects (not overlapping)

FUTURE:
=======
* yet another REDESIGN: separate 'UML' notation stuff from 'dia' specific classes
=> have XP classes which represent a UML diagram - independent of 'dia'
=> have an 'Visitor' which goes through the UML diagram structure and creates a 'dia' diagram from it
=> OR just use annotations?

