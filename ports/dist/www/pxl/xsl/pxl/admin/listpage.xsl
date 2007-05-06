<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
 !
 ! $Id: static.xsl 7662 2006-08-13 11:47:23Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:include href="../layout.inc.xsl"/>

  <xsl:template name="page-body">
    <table border="0" class="table-list">
      <tr>
        <th>Page ID</th>
        <th>Page</th>
        <th>Comment information</th>
        <th>Publishing date</th>
        <th>Actions</th>
      </tr>
      
      <xsl:for-each select="/formresult/pages/page">
        <tr>
          <td><xsl:value-of select="@page_id"/></td>
          <td><xsl:value-of select="@title"/></td>
          <td>...</td>
          <td>
            <xsl:if test="published = ''">-</xsl:if>
            <xsl:if test="published != ''">
              <xsl:value-of select="func:date(published)"/>
            </xsl:if>
          </td>
          <td>
            <a href="{func:link(concat('admin/listpage?page=', @page_id, '&amp;action=move.up'))}">Up</a> |
            <a href="{func:link(concat('admin/listpage?page=', @page_id, '&amp;action=move.dn'))}">Down</a> |
            <a href="{func:link(concat('admin/editpage?page=', @page_id, '&amp;action=edit'))}">Edit</a> |
            <a href="{func:link(concat('admin/listpage?page=', @page_id, '&amp;action=delete'))}">Delete</a>
          </td>
        </tr>
      </xsl:for-each>
      <tr>
        <td colspan="4">&#160;</td>
        <td>
          <a href="{func:link('admin/editpage')}">Create new page...</a>
        </td>
      </tr>
    </table>
  </xsl:template>
</xsl:stylesheet>
