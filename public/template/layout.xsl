<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output method="html" doctype-system="about:legacy-compat" version="5.0" encoding="UTF-8" omit-xml-declaration="yes" indent="yes"/>
    <xsl:strip-space elements="*"/>
    <xsl:param name="template-version">?v20210322</xsl:param>
    <xsl:param name="css"/>
    <xsl:param name="javascript"/>
    <xsl:param name="title"/>
    <xsl:template match="/">
        <html 	xmlns="http://www.w3.org/1999/xhtml" 		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 		xsi:schemaLocation="http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd"      	xml:lang="ru" lang="ru">
            <head>
                <title>
                    <xsl:choose>
                        <xsl:when test="$title"><xsl:value-of select="$title"/> / LazyFramework Install</xsl:when>
                        <xsl:otherwise>Install / LazyFramework</xsl:otherwise>
                    </xsl:choose>
                </title>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                <meta http-equiv="Content-Language" content="EN"/>
                <meta charset="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, minimum-scale=1, user-scalable=yes"/>
                <meta name="theme-color" content="#101030"/>
                <link href="{concat('/css/default.css', $template-version)}" rel="stylesheet" type="text/css"/>
                <xsl:if test="$css">
                    <xsl:copy-of select="$css"/>
                </xsl:if>
                <script type="text/javascript" language="javascript" src="{concat('/script/common.js',$template-version)}"/>
                <xsl:if test="$javascript">
                    <xsl:copy-of select="$javascript"/>
                </xsl:if>
            </head>
            <body>
                <main>
                    <xsl:apply-templates mode="body"/>
                </main>
                <section>
                    <xsl:apply-templates mode="content"/>
                </section>
                <footer>For more information visit <a href="http://www.lazyframework.ru" rel="nofollow">LazyFramework</a>.</footer>
            </body>
        </html>
    </xsl:template>
    <xsl:template match="exception|message" mode="body">
        <header>
            <h1>
                <xsl:choose>
                    <xsl:when test="@title"><xsl:value-of select="@title"/></xsl:when>
                    <xsl:when test="exception">Error</xsl:when>
                    <xsl:otherwise>Warning</xsl:otherwise>
                </xsl:choose>
            </h1>
        </header>
        <article>
            <div class="{name(.)}">
                <p><xsl:value-of select="@message"/></p>
                <p><xsl:value-of select="@debug"/></p>
            </div>
            <a href="/" class="button">Return to main</a>
        </article>
    </xsl:template>
    <xsl:template match="content" mode="content">
        <article>
            <ul class="content">
                <xsl:apply-templates mode="content"/>
            </ul>
        </article>
    </xsl:template>
    <xsl:template match="entry" mode="content">
        <li>
            <h3><xsl:value-of select="@title"/></h3>
            <p><xsl:value-of select="@description"/>...</p>
            <a href="/content/entry.xml?contentID={@id}" class="button">Details</a>
        </li>
    </xsl:template>
</xsl:stylesheet>