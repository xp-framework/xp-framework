<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0"
>
  
  <!-- Helper stylesheets for docu-generation -->
  
  <xsl:param name="mode" select="'packages'"/>
  <xsl:param name="package" select="''"/>
  <xsl:param name="collection" select="''"/>
  <xsl:output method="text" encoding="iso-8859-1" indent="no" omit-xml-declaration="yes"/>
  
  <xsl:template match="packages">

    <!-- List all packages is the classtree -->
    <xsl:if test="$mode = 'packages'">
<xsl:apply-templates select="//package"/>
    </xsl:if>
    
    <!-- List all collections in specified package -->
    <xsl:if test="$mode = 'collection'">
<xsl:apply-templates select="//package[@type = $package]//collection"/>
    </xsl:if>
    
    <!-- List *all* collections -->
    <xsl:if test="$mode = 'allcollections'">
<xsl:apply-templates select="//collection"/>
    </xsl:if>
    
    <!-- List *all* classes -->
    <xsl:if test="$mode = 'allclasses'">
<xsl:apply-templates select="//collection/class"/>
    </xsl:if>

  </xsl:template>
  
  <xsl:template match="package">
<xsl:value-of select="./@type"/>
<xsl:text>
</xsl:text>
  </xsl:template>
  
  <xsl:template match="collection">
<xsl:value-of select="./@prefix"/>
<xsl:text>
</xsl:text>
  </xsl:template>
  
  <xsl:template match="class">
<xsl:value-of select="./@className"/>
<xsl:text>
</xsl:text>
  </xsl:template>
  
</xsl:stylesheet>
