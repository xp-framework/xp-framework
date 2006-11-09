<?xml version="1.0"?>
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="exsl func"
>

  <xsl:output method="html"/>

  <func:function name="func:first-sentence">
    <xsl:param name="comment"/>
    
    <func:result>
      <xsl:value-of select="substring-before(concat(translate($comment, '&#10;', ' '), '. '), '. ')"/>
    </func:result>
  </func:function>

  <func:function name="func:ltrim">
    <xsl:param name="text"/>
    <xsl:param name="chars"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="contains(substring($text, 1, 1), $chars)">
          <xsl:value-of select="func:ltrim(substring($text, 2, string-length($text)), $chars)"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$text"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <xsl:template match="class">
    <h1>Apidoc: <xsl:value-of select="concat(@type, ' ', @name)"/></h1>

    <h2>Purpose: <xsl:value-of select="purpose"/></h2>
    
    <xsl:if test="deprecated">
      <h5>
        Deprecated!
        <xsl:value-of select="deprecated" disable-output-escaping="yes"/>
      </h5>
    </xsl:if>
    
    <p class="comment">
      <xsl:value-of select="comment" disable-output-escaping="yes"/>
    </p>
    
    <h3>Inheritance</h3>
    <xsl:for-each select="extends/link">
      <a href="?{@href}"><xsl:value-of select="@href"/></a>
      <br/>
    </xsl:for-each>

    <!-- Fields -->
    <h3>Field summary</h3>
    <ul>
      <xsl:for-each select="fields[not(@from)]/field">
        <li>
          <a name="@name"><b><xsl:value-of select="@name"/></b></a><br/>
          <xsl:choose>
            <xsl:when test="string(.) != ''">Initial value: <tt><xsl:value-of select="."/></tt></xsl:when>
            <xsl:otherwise>(no initial value)</xsl:otherwise>
          </xsl:choose>
        </li>
      </xsl:for-each>
    </ul>

    <!-- Inherited fields -->
    <xsl:for-each select="fields[@from]">
      <xsl:if test="count(method) &gt; 0">
        <h3>Fields inherited from <a href="?{@from}"><xsl:value-of select="@from"/></a></h3>

        <xsl:for-each select="field">
          <a href="?{../@from}#{@name}"><xsl:value-of select="@name"/></a>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
        <br clear="all"/>
      </xsl:if>
    </xsl:for-each>
    
    <!-- Methods -->
    <h3>Method summary</h3>
    <ul>
      <xsl:for-each select="methods[not(@from)]/method">
        <li>
          <a href="#{@name}"><xsl:value-of select="concat(@access, ' ', @return, ' ', @name)"/>
            <xsl:text>(</xsl:text>
            <xsl:for-each select="argument">
              <xsl:value-of select="@name"/>
              <xsl:if test="position() != last()">, </xsl:if>
            </xsl:for-each>
            <xsl:text>)</xsl:text>
          </a><br/>
          <xsl:value-of select="func:first-sentence(comment)" disable-output-escaping="yes"/>
        </li>
      </xsl:for-each>
    </ul>

    <!-- Inherited methods -->
    <xsl:for-each select="methods[@from]">
      <xsl:if test="count(method) &gt; 0">
        <h3>Methods inherited from <a href="?{@from}"><xsl:value-of select="@from"/></a></h3>

        <xsl:for-each select="method">
          <a href="?{../@from}#{@name}"><xsl:value-of select="@name"/></a>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
        <br clear="all"/>
      </xsl:if>
    </xsl:for-each>

    <h3>Method details</h3>
    <xsl:for-each select="methods[not(@from)]/method">
      <a name="{@name}"/>
      <h4>
        <xsl:value-of select="@access"/>
        <xsl:text> </xsl:text>
        <a>
          <xsl:if test="contains(@return, '.')"><xsl:attribute name="href">
            <xsl:text>?</xsl:text>
            <xsl:value-of select="func:ltrim(substring-before(concat(@return, '['), '['), '&amp;')"/>
          </xsl:attribute></xsl:if>
          <xsl:value-of select="@return"/>
        </a>
        <xsl:text> </xsl:text>
        <xsl:value-of select="@name"/>
        <xsl:text>(</xsl:text>
        <xsl:for-each select="argument">
          <xsl:value-of select="@name"/>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
        <xsl:text>)</xsl:text>
      </h4>
      <p class="comment">
        <xsl:value-of select="comment" disable-output-escaping="yes"/>
      </p>
      <hr/>
    </xsl:for-each>
  </xsl:template>

  <xsl:template match="/">
    <style type="text/css">
      body {
        font-family: "Verdana", "Arial", sans-serif;
      }
      h1, h2, h3 {
        font-family: "Lucida Grande", "Verdana", "Arial", sans-serif;
      }
      h3 {
        border-bottom: 1px solid black; margin-bottom: 20px 
      }
      p.comment {
        white-space: pre;
      }
    </style>
    <a href="?">Back to list</a><hr/>

    <xsl:apply-templates select="doc"/>
  </xsl:template>
</xsl:stylesheet>
