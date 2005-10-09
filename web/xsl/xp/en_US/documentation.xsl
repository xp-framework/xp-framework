<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:include href="../layout.xsl"/>
  
  <!--
   ! Template for context navigation
   !
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">

    <!-- cvs -->
    <h4 class="context">Repository</h4>
    <ul class="context">
      <li>
        <em>svn-web</em>:<br/>
        <a href="#">(not yet available)</a>
      </li>
      <li>
        <em>Inheritance tree</em>:<br/>
        <a href="{func:link('documentation/inheritance')}">click here</a>
      </li>
    </ul>

    <!-- see also -->
    <h4 class="context">See also</h4>
    <ul class="context">
      <li>
        <em>(development)</em>:<br/>
        <a href="#devel/apidoc">API doc howto</a>
      </li>
    </ul>

  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h1>api documentation</h1>

    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <xsl:for-each select="document('../../../build/collections.xml')/collections/collection">
        <tr><td colspan="3">
          <h3><xsl:value-of select="@name"/> collection (<xsl:value-of select="count(.//class)"/> classes)</h3>
        </td></tr>
        <tr><td colspan="3"><img width="1" height="8" src="/image/blank.gif"/></td></tr>
        <xsl:for-each select="package">
          <tr>
            <td width="1%" valign="top" nowrap="nowrap"><img width="17" height="17" hspace="2" src="/image/package.gif"/>&#160;</td>
            <td width="10%" valign="top" nowrap="nowrap">
              <b>
                <a href="documentation/package?{../@name}/{@name}"><xsl:value-of select="@name"/></a>
              </b> (<xsl:value-of select="@packages"/>) 
              <img src="/image/dot.gif" border="0" height="7" width="7" alt="&gt;"/>
              &#160;
            </td>
            <td width="89%" valign="top">
              <xsl:for-each select="package">
                <a title="{@name}" href="documentation/package?{../../@name}/{@name}">
                  <xsl:value-of select="substring(@name, string-length(../@name)+ 2, string-length(@name))"/>
                </a>
                <xsl:if test="position() &lt; last()">, </xsl:if>
              </xsl:for-each>
              <br/><br/>
            </td>
          </tr>
        </xsl:for-each>
      </xsl:for-each>
    </table>
  </xsl:template>
  
</xsl:stylesheet>
