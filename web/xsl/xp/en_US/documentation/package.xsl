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
  <xsl:include href="../../layout.xsl"/>
  
  <!--
   ! Template for context navigation
   !
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">

    <!-- cvs -->
    <h4 class="context">CVS activity</h4>
    <ul class="context">
      <li>
        <em>2003-12-11 17:08</em>:<br/>
        <a href="#apidoc/classes/ch/ecma/StliConnection">StliConnection</a> (friebe)
      </li>
      <li>
        <em>2003-12-11 17:08</em>:<br/>
        <a href="#apidoc/classes/ch/ecma/StliConnection">TelephonyAddress</a> (friebe)
      </li>
      <li>
        <em>2003-09-27 15:30:00</em>:<br/>
        <a href="#apidoc/classes/com/sun/webstart/JnlpDocument">JnlpDocument</a> (friebe)
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
    <xsl:variable name="current" select="/formresult/breadcrumb/current"/>
    
    <h1>
      <a href="../documentation">api documentation</a> (<xsl:value-of select="$current/@collection"/>)
      <xsl:for-each select="/formresult/breadcrumb/path">
        :: <a href="package?{$current/@collection}/{@qualified}"><xsl:value-of select="."/></a>
      </xsl:for-each>
    </h1>
    
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <xsl:for-each select="document('../../../../build/collections.xml')/collections/collection[@name = $current/@collection]//package[@name = $current/@package]">
        <tr><td colspan="3">
          <h3><xsl:value-of select="@name"/> package (<xsl:value-of select="@classes"/> classes)</h3>
        </td></tr>
        <tr><td colspan="3"><img width="1" height="8" src="/image/blank.gif"/></td></tr>
        <xsl:for-each select="package">
          <tr>
            <td width="1%" valign="top" nowrap="nowrap"><img width="17" height="17" hspace="2" src="/image/package.gif"/>&#160;</td>
            <td width="10%" valign="top" nowrap="nowrap">
              <b>
                <a href="package?{$current/@collection}/{@name}"><xsl:value-of select="@name"/></a>
              </b> (<xsl:value-of select="@packages"/>) 
              <img src="/image/dot.gif" border="0" height="7" width="7" alt="&gt;"/>
              &#160;
            </td>
            <td width="89%" valign="top">
              <xsl:for-each select="package">
                <a title="{@name}" href="package?{$current/@collection}/{@name}"><xsl:value-of select="substring-after(@name, '.')"/></a>
                <xsl:if test="position() &lt; last()">, </xsl:if>
              </xsl:for-each>
              <br/><br/>
            </td>
          </tr>
        </xsl:for-each>
        <xsl:for-each select="class">
          <tr>
            <td width="1%" valign="top" nowrap="nowrap"><img width="17" height="17" hspace="2" src="/image/class.gif"/>&#160;</td>
            <td width="99%" valign="top" nowrap="nowrap" colspan="2">
              <b>
                <a href="class?{$current/@collection}/{@name}"><xsl:value-of select="@name"/></a>
              </b>
              <br/><br/>
            </td>
          </tr>
        </xsl:for-each>
        <xsl:for-each select="sapi">
          <tr>
            <td width="1%" valign="top" nowrap="nowrap"><img width="17" height="17" hspace="2" src="/image/class.gif"/>&#160;</td>
            <td width="99%" valign="top" nowrap="nowrap" colspan="2">
              <b>
                <a href="sapi?{$current/@collection}/{@name}"><xsl:value-of select="@name"/></a>
              </b>
              <br/><br/>
            </td>
          </tr>
        </xsl:for-each>

      </xsl:for-each>
    </table>
  </xsl:template>
  
</xsl:stylesheet>
