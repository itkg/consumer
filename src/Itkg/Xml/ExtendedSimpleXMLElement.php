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
    /**
     * Add cdata section
     * 
     * @param $cdataText
     */
    public function addCData($cdataText)
    {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild(
            $no->createCDATASection($cdataText)
        );
    }
}
