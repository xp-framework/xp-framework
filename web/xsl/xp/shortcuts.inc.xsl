<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet include for shortcuts icons
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

  <!--
   ! Function to display a serialized date object (date only)
   !
   ! @param  node-set shortcuts
   !-->
  <func:function name="func:shortcuts">
    <xsl:param name="shortcuts"/>

    <func:result>
      <table class="shortcuts">
        <tr>
          <xsl:for-each select="exsl:node-set($shortcuts)/shortcut">
            <td align="middle" valign="top">
              <a href="{@href}">
                <img width="32" height="32" border="0" src="/image/icons/{@icon}.png"/><br/>
                <xsl:value-of select="."/>
              </a>
            </td>
          </xsl:for-each>
        </tr>
      </table>
    </func:result>
  </func:function>

</xsl:stylesheet>
