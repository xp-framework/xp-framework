<?xml version="1.0"?>
<xsl:stylesheet
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:exsl="http://exslt.org/common"
 xmlns:func="http://exslt.org/functions"
 xmlns:dyn="http://exslt.org/dynamic"
 extension-element-prefixes="exsl func dyn"
 version="1.0"
>
  <xsl:output
   method="text"
   encoding="iso-8859-1"
  />
  <xsl:variable name="__lang" select="'de_DE'"/>
  
  <xsl:variable name="texts">
    <group name="common">
      <group name="domain">
        <text name="name">
          <de_DE>Domainname</de_DE>
          <en_UK>Domain name</en_UK>
        </text>
      </group>
    </group>
  </xsl:variable>

  <xsl:template match="/">
    <xsl:value-of select="func:get_text('common#domain')"/>
  </xsl:template>
  
  <func:function name="func:get_text">
    <xsl:param name="name"/>
    <xsl:param name="query" select="''"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="contains($name, '#')">
          <xsl:variable name="newquery"><xsl:value-of select="$query"/>group[@name= '<xsl:value-of select="substring-before($name, '#')"/>']/</xsl:variable>
        
          <xsl:copy-of select="func:get_text(substring-after($name, '#'), $newquery)"/>
        </xsl:when>
        
        <xsl:otherwise>
          <xsl:value-of select="concat('$texts/', $query, '*[name() = $__lang]')"/>
          <xsl:copy-of select="dyn:evaluate(concat('$texts/', $query, '*[name() = $__lang]'))"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>
</xsl:stylesheet>
