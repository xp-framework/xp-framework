<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:output method="text" encoding="iso-8859-1" indent="no"/>
  
  <func:function name="func:ws">
    <xsl:param name="count"/>
    <func:result>
      <xsl:if test="$count &gt;= 0">
        <xsl:text> </xsl:text>
        <xsl:value-of select="func:ws($count - 1)"/>
      </xsl:if>
    </func:result>
  </func:function>
 
  <func:function name="func:indent">
    <xsl:param name="str"/>
    <func:result>
      <xsl:value-of select="$str"/><xsl:value-of select="func:ws(40 - string-length($str))"/>
    </func:result>
  </func:function>
  
  <xsl:template match="/">
<xsl:text>Service                                  Total  Passed Failed
=============================================================
</xsl:text>
    <xsl:for-each select="clients/client">
      <xsl:variable name="name" select="@name"/>
      <xsl:value-of select="func:indent($name)"/>
      <xsl:value-of select="format-number(count(method), '###')"/>
      <xsl:value-of select="func:ws(5)"/>
      <xsl:value-of select="format-number(count(method[not(@error)]), '###')"/>
      <xsl:value-of select="func:ws(5)"/>
      <xsl:value-of select="format-number(count(method[@error= 'permanent']), '###')"/>
<xsl:text>
</xsl:text>
    </xsl:for-each>
<xsl:text>=============================================================
</xsl:text>Overall<xsl:value-of select="func:ws(32)"/>
    <xsl:value-of select="format-number(count(clients/client/method), '###')"/>
    <xsl:value-of select="func:ws(5)"/>
    <xsl:value-of select="format-number(count(clients/client/method[not(@error)]), '###')"/>
    <xsl:value-of select="func:ws(5)"/>
    <xsl:value-of select="format-number(count(clients/client/method[@error = 'permanent']), '###')"/>
<xsl:text>
</xsl:text>
  </xsl:template>
  
</xsl:stylesheet>
