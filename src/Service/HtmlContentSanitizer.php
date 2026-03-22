<?php

namespace App\Service;

/**
 * @brief Sanitizes a limited subset of HTML for safe rendering.
 *
 * The goal is to allow typical rich-text formatting while preventing
 * script execution and dangerous attributes.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
class HtmlContentSanitizer
{
    /**
     * @brief Sanitizes an HTML fragment using a strict whitelist.
     *
     * @param string $html The HTML fragment to sanitize.
     * @return string The sanitized HTML fragment.
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function sanitize(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        $allowedTags = [
            'p',
            'br',
            'strong',
            'b',
            'em',
            'i',
            'u',
            'ul',
            'ol',
            'li',
            'a',
            'blockquote',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
        ];

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML(
            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html,
            \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD
        );
        libxml_use_internal_errors($previous);

        $xpath = new \DOMXPath($doc);
        $nodes = $xpath->query('//*');
        if ($nodes === false) {
            return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        foreach ($nodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);

            if (!in_array($tag, $allowedTags, true)) {
                // Replace disallowed nodes with their text content.
                $text = $node->textContent ?? '';
                $node->parentNode?->replaceChild($doc->createTextNode($text), $node);
                continue;
            }

            if ($tag === 'a') {
                $href = $node->getAttribute('href');
                if ($href === '') {
                    // No href => remove link to avoid accidental navigation.
                    $node->removeAttribute('href');
                    continue;
                }

                $href = trim($href);
                $isSafe = preg_match('/^(https?:\\/\\/|mailto:|tel:|\\/|#)/i', $href) === 1;

                if (!$isSafe) {
                    // Remove unsafe href.
                    $node->removeAttribute('href');
                }

                // Remove all other attributes except href.
                if ($node->hasAttributes()) {
                    $toRemove = [];
                    foreach ($node->attributes as $attr) {
                        if ($attr instanceof \DOMAttr) {
                            if (strtolower($attr->name) !== 'href') {
                                $toRemove[] = $attr->name;
                            }
                        }
                    }

                    foreach ($toRemove as $name) {
                        $node->removeAttribute($name);
                    }
                }
            } else {
                // Remove all attributes for non-anchor tags.
                if ($node->hasAttributes()) {
                    while ($node->attributes->length > 0) {
                        $attr = $node->attributes->item(0);
                        if ($attr !== null) {
                            $node->removeAttribute($attr->name);
                        }
                    }
                }
            }
        }

        $body = $doc->getElementsByTagName('body')->item(0);
        if (!$body instanceof \DOMElement) {
            return '';
        }

        $out = '';
        foreach ($body->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return $out;
    }
}

