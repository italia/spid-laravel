<?php
/**
 * Trait providing safe XML extraction methods for SPID authentication events.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth\Events\Concerns;

use DOMDocument;
use Exception;
use OneLogin\Saml2\Utils as SAMLUtils;

trait SafeXmlExtraction
{
    /**
     * Safely query XPath and return attribute or text content.
     * Never throws exceptions - returns null if anything fails.
     *
     * @param DOMDocument|null $document The DOMDocument to query
     * @param string $xpath XPath query
     * @param string|null $attribute Attribute name to extract (null for text content)
     * @return string|null Extracted value or null if not found/failed
     */
    protected function safeXPathQuery(?DOMDocument $document, string $xpath, ?string $attribute = null): ?string
    {
        if ($document === null) {
            return null;
        }

        try {
            $nodes = SAMLUtils::query($document, $xpath);
            if ($nodes->length === 0) {
                return null;
            }

            $node = $nodes->item(0);
            if ($node === null) {
                return null;
            }

            if ($attribute !== null) {
                if (!$node->hasAttribute($attribute)) {
                    return null;
                }
                $value = $node->getAttribute($attribute);
                return $value !== '' ? $value : null;
            }

            $textContent = trim($node->textContent);
            return $textContent !== '' ? $textContent : null;
        } catch (Exception $e) {
            return null;
        }
    }
}
