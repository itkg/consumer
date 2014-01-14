<?php

namespace Itkg\Xml;

/**
 * Extension for SimpleXMLElement
 *
 * @author Fatma ARKAM <fatma.arkam@businessdecision.com>
 *
 * @package \Itkg\Xml
 */
class ExtendedSimpleXMLElement extends \SimpleXMLElement
{
    public function addCData($cdata_text)
    {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild(
            $no->createCDATASection($cdata_text)
        );
    }
}
