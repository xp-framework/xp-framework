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
  
  <!-- Texts file, usually included by document() -->
  <xsl:variable name="texts">
    <group name="common">
      <text name="domain">
        <de_DE>Plain snippet domain name</de_DE>
      </text>
      <group name="domain">
        <text name="name">
          <de_DE>Domainname</de_DE>
          <en_UK>Domain name</en_UK>
        </text>
      </group>
    </group>
  </xsl:variable>

  <xsl:template match="/">
  
    <!-- Standard text, grouped one level -->
    <xsl:value-of select="func:get_text('common#domain')"/>
    
    <!-- Two level of groups, can be an arbitrary number of; will become slower with each group -->
    <xsl:value-of select="func:get_text('common#domain#name')"/>
  </xsl:template>
  
  <func:function name="func:get_text">
    <xsl:param name="name"/>
    <xsl:param name="query" select="''"/>
    
    <func:result>
      <xsl:choose>
      
        <!--
         ! If snippet name still contains '#', we'll have to dive one level deeper, append 
         ! another condition for the xpath and cut off the processed part from the snippets
         ! symbolic name
         !-->
        <xsl:when test="contains($name, '#')">
          <xsl:variable name="newquery"><xsl:value-of select="$query"/>group[@name= '<xsl:value-of select="substring-before($name, '#')"/>']/</xsl:variable>
        
          <xsl:copy-of select="func:get_text(substring-after($name, '#'), $newquery)"/>
        </xsl:when>
        
        <!--
         ! Condition complete, apply
         !-->
        <xsl:otherwise>
          <xsl:variable name="expr">exsl:node-set($texts)/<xsl:value-of select="$query"/>text[@name= '<xsl:value-of select="$name"/>']/*[name()= $__lang]</xsl:variable>
          <xsl:copy-of select="dyn:evaluate($expr)"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>
</xsl:stylesheet>
