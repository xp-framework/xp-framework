<xsl:stylesheet 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  version="1.0"
>
  <xsl:output method="html" encoding="iso-8859-1"/>
  
  <xsl:template match="classdoc">
    <html>
      <head>
        <title>Class <xsl:value-of select="@classname"/></title>
      </head>
      <body>
        <h1>Class <xsl:value-of select="@classname"/></h1>
        <h2><xsl:value-of select="comments/file/cvsver"/></h2>
        <hr/>
        <h2>Summary</h2>
        <xsl:apply-templates select="comments/class"/>
        <hr/>
        <xsl:if test="count(defines) &gt; 0">
          <h2>Defines</h2>
          <table>
            <xsl:for-each select="defines/*">
              <tr>
                <td><pre><xsl:value-of select="name()"/></pre></td>
                <td><xsl:value-of select="."/></td>
              </tr>
            </xsl:for-each>
          </table>
          <hr/>
        </xsl:if>
        
        <h2>Method Index</h2>
        <xsl:for-each select="comments/function/*">
          <a href="#{name()}"><xsl:value-of select="name()"/></a>
          <blockquote>
            <xsl:value-of select="substring-before(text, '&#13;')"/>
          </blockquote>
        </xsl:for-each>
        <hr/>
        <h2>Methods</h2>
        <xsl:apply-templates select="comments/function"/>
        <hr/>
        <small>Copyright 2002</small>
      </body>
    </html>
  </xsl:template>
  
  <xsl:template match="pre">
    <pre><xsl:apply-templates/></pre>
  </xsl:template>

  <xsl:template match="code">
    <code><xsl:apply-templates/></code>
  </xsl:template>

  <xsl:template match="code//*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>
  
  <xsl:template match="reference">
    <xsl:variable name="link">
      <xsl:choose>
        <xsl:when test="scheme = 'xp'">xp/</xsl:when>
      </xsl:choose>
    </xsl:variable>
    
    <a href="{$link}">
      <xsl:value-of select="$link"/>
    </a>
  </xsl:template>
  
  <xsl:template match="comments/class">
    <h4>
      <xsl:value-of select="name"/> extends <xsl:value-of select="extends"/>
    </h4>
    <p>
      <xsl:apply-templates select="./text"/>
    </p>
    <xsl:if test="count(references/reference) &gt; 0">
      <p>
        <b>See also</b>
        <ul>
          <xsl:for-each select="references/reference">
            <li><xsl:apply-templates/></li>
          </xsl:for-each>
        </ul>
      </p>
    </xsl:if>
  </xsl:template>
  
  <xsl:template match="comments/function/*">
    <h3>
      <li>
        <a name="{name()}"><xsl:value-of select="name()"/></a>
      </li>
    </h3>
    <blockquote>
      <code>
        <xsl:value-of select="access"/>
        <xsl:text> </xsl:text>
        <xsl:choose>
          <xsl:when test="return/type">
            <a href="{return/type}">
              <xsl:value-of select="return/type"/>
            </a>
          </xsl:when>
          <xsl:otherwise>void</xsl:otherwise>
        </xsl:choose>
        <xsl:text> </xsl:text>
        <xsl:if test="return/reference = 1">&amp;</xsl:if>
        <xsl:value-of select="name()"/>
        <xsl:text>(</xsl:text>
        <xsl:for-each select="params/param">
          <a href="{type}"><xsl:value-of select="type"/></a>
          <xsl:text> </xsl:text>
          <xsl:value-of select="name"/>
          <xsl:if test="position() != last()">, </xsl:if>
        </xsl:for-each>
        <xsl:text>)</xsl:text>
      </code>
    
      <p>
        <xsl:apply-templates select="./text"/>
      </p>
      <xsl:if test="count(params/param) &gt; 0">
        <p>
          <b>Parameters</b>
          <ul>
            <xsl:for-each select="params/param">
              <li><xsl:value-of select="name"/> - <xsl:value-of select="description"/></li>
            </xsl:for-each>
          </ul>
        </p>
      </xsl:if>
      <xsl:if test="return/type">
        <p>
          <b>Returns</b>
          <ul>
            <li>
              <xsl:value-of select="return/description"/>
            </li>
          </ul>
        </p>
      </xsl:if>
      <xsl:if test="count(throws/throw) &gt; 0">
        <p>
          <b>Throws</b>
          <ul>
            <xsl:for-each select="throws/throw">
              <li><a href="{exception}"><xsl:value-of select="exception"/></a> - <xsl:value-of select="condition"/></li>
            </xsl:for-each>
          </ul>
        </p>
      </xsl:if>
      <xsl:if test="count(references/reference) &gt; 0">
        <p>
          <b>See also</b>
          <ul>
            <xsl:for-each select="references/reference">
              <li><a href="{.}"><xsl:value-of select="."/></a></li>
            </xsl:for-each>
          </ul>
        </p>
      </xsl:if>
    </blockquote>
  </xsl:template>
  
</xsl:stylesheet>
