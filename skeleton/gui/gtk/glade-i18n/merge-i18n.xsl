<?xml version="1.0" encoding="iso-8859-1" ?>
<xsl:stylesheet 
  version="1.0" 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
  <xsl:output method="xml" encoding="iso-8859-1" omit-xml-declaration="no" indent="no"/>
  <xsl:param name="language" select="'C'"/>
  <xsl:param name="sources" select="/GTK-Interface/project/program_name/text()"/>
  <xsl:variable name="texts" select="document(concat($sources, '.i18n.xml'))/texts"/>
  
  <!-- Get text -->
  <xsl:template name="gettext">
    <xsl:param name="name"/>
	<xsl:param name="data"/>
	<xsl:param name="text" select="$texts/text[attribute::for=$name]/language[attribute::name=$language]"/>
	
	<xsl:choose>
  	  <xsl:when test="not($text)">
  	    <xsl:message>
	      Could'nt locate text <xsl:value-of select="$name"/> in language <xsl:value-of select="$language"/>,
		  using default "<xsl:value-of select="$data"/>".
        </xsl:message>
		<xsl:value-of select="$data"/>
  	  </xsl:when>
	  <xsl:otherwise>
	    <xsl:value-of select="$text"/>
	  </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <!-- Language -->
  <xsl:template match="/GTK-Interface/project/language">
    <language>
	  <xsl:value-of select="$language"/>
	</language>
  </xsl:template>
  
  <!-- Window title -->
  <xsl:template match="/GTK-Interface/widget/title">
    <title>
	  <xsl:call-template name="gettext">
	    <xsl:with-param name="name" select="../name"/>
		<xsl:with-param name="data" select="./text()"/>
      </xsl:call-template>
	</title>
  </xsl:template>
  
  <!-- Widget labels -->
  <xsl:template match="widget/label">
    <label>
	  <xsl:call-template name="gettext">
	    <xsl:with-param name="name" select="../name"/>
		<xsl:with-param name="data" select="./text()"/>
      </xsl:call-template>
	</label>
  </xsl:template>

  <!-- The rest -->
  <xsl:template match="*|@*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>

</xsl:stylesheet>
