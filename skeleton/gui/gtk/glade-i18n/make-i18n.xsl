<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet 
  version="1.0" 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
  <xsl:output method="xml" encoding="iso-8859-1" omit-xml-declaration="no" indent="no"/>
  <xsl:param name="sources"/>
  <xsl:param name="glade"/>
  <xsl:param name="languages" select="document($sources)/languages"/>
  
  <xsl:template match="/">
    <xsl:text>&#10;</xsl:text>
    <texts apply="{$glade}">
      <xsl:apply-templates/>
	  <xsl:text>&#10;</xsl:text>
	</texts>
  </xsl:template>	
  
  <!-- Get text -->
  <xsl:template name="maketext">
    <xsl:param name="name"/>
	<xsl:param name="data"/>
	
	<xsl:text>&#10;  </xsl:text>
	<text for="{$name}">
	  <xsl:for-each select="$languages/language">
	    <xsl:text>&#10;    </xsl:text>
	    <language name="{@name}">
          <xsl:choose>
		    <xsl:when test="@name='C'"><xsl:value-of select="$data"/></xsl:when>
			<xsl:otherwise>***</xsl:otherwise>
		  </xsl:choose>
	    </language>
	  </xsl:for-each>
	  <xsl:text>&#10;  </xsl:text>
	</text>
  </xsl:template>
  
  <!-- Window title -->
  <xsl:template match="/GTK-Interface/widget/title">
	<xsl:call-template name="maketext">
	  <xsl:with-param name="name" select="../name"/>
	  <xsl:with-param name="data" select="./text()"/>
    </xsl:call-template>
  </xsl:template>
  
  <!-- Widget labels -->
  <xsl:template match="widget/label">
	<xsl:call-template name="maketext">
	  <xsl:with-param name="name" select="../name"/>
	  <xsl:with-param name="data" select="./text()"/>
    </xsl:call-template>
  </xsl:template>

  <!-- The rest: discard -->
  <xsl:template match="text()">
  </xsl:template>

</xsl:stylesheet>
