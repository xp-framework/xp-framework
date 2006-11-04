<?xml version="1.0"?>
<xsl:stylesheet 
 version="1.0" 
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:msxsl="urn:schemas-microsoft-com:xslt"
 xmlns:func="http://exslt.org/functions"
>

  <!--
  <func:function name="func:hello">
    <xsl:param name="name"/>
    
    <func:result>
      <xsl:value-of select="concat('Hello ', $name)"/>
    </func:result>
  </func:function>
  -->
  
  <msxsl:script language="JScript" implements-prefix="func"><![CDATA[
    function nodeString(node) {
      switch (node.nodeType) {
        case 1: return stringOf(node.childNodes);
        case 2: return node.text;
        case 3: return node.nodeValue;
        case undefined: return '';
      }
      throw 'Encountered unknown node # ' + node.nodeType + ' (' + node.nodeTypeString + ')';
    }
    
    function stringOf(arg) {
      switch (typeof(arg)) {
        case 'string': return arg;
        case 'object': {
          if (arg.length) {
            var str= '';
            for (var i= 0; i < arg.length; i++) {
              str+= nodeString(arg.item(i));
            }
            return str;
          }
          return nodeString(arg);
        }
      }        
    }

    function hello(node) {
      return 'Hello ' + stringOf(node) + "\n";
    }
  ]]></msxsl:script>
  
  <xsl:template match="/">
    <xsl:value-of select="func:hello(/document/date/month)"/>
    <xsl:value-of select="func:hello(/document/date/month/text())"/>
    <xsl:value-of select="func:hello(/document/date/@utime)"/>
    <xsl:value-of select="func:hello(/document/date/text())"/>
    <xsl:value-of select="func:hello('Moto')"/>
  </xsl:template>
</xsl:stylesheet>
