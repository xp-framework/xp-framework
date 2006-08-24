How to use 'xp://org.dia.*'
===========================
@author: Pascal Sick (sick@schlund.de)

HOWTO: create DIAgrams:
-----------------------

> php doclet.php --doclet=org.dia.DiaDoclet --verbose --recurse --gzipped --directory=/diagrams --file=test.dia util.Date lang.XPClass [...]

DiaDoclet options:
* --verbose
* --recurse
* --gzipped
* --directory=$DIR
* --file=$FILE

FILES:
======
DiaMarshaller : create DiaUMLClass from XP class
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

* Doclet
DiaDoclet : doclet which loops over the given classes (recursively) and
generates an DIAgram representation for each class. Returns a DIAgram with all
classes in it.
