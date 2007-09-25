<?xml version="1.0"?>
<xsl:stylesheet
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:exsl="http://exslt.org/common"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="exsl func"
 version="1.0"
>
  <xsl:output
   method="text"
   encoding="iso-8859-1"
  />
  <xsl:variable name="__lang" select="'de_DE'"/>

  <xsl:template match="/">
    <xsl:value-of select="func:get_text('common#domain')"/>
  </xsl:template>
  
  <xsl:variable name="texts">
    <group name="common">
      <text name="domain">
        <de_DE>Domainname</de_DE>
        <en_UK>Domain name</en_UK>
      </text>
    </group>
  </xsl:variable>

  <func:function name="func:get_text">
    <xsl:param name="name"/>
    
    <xsl:variable name="group" select="substring-before($name, '#')"/>
    <xsl:variable name="snippet" select="substring-after($name, '#')"/>

    <func:result>
      <xsl:copy-of select="exsl:node-set($texts)/group[@name= $group]/text[@name= $snippet]/*[name() = $__lang]"/>
    </func:result>
  </func:function>
</xsl:stylesheet>
