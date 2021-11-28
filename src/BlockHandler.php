<?php

namespace Broutard\NovaEditorJs;

use Illuminate\Support\Arr;

class BlockHandler
{
    public function handleLink($link)
    {
        try {
            if ($xml = new \SimpleXMLElement($link, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_COMPACT)) {
                $attrs = $xml->attributes();

                if ($data = (string)$attrs['data-link']) {
                    $data = json_decode($data, true);

                    // generate url
                    if ($route = Arr::get($data, 'route.name')) {
                        $attrs['href'] = app('url')->route($route, Arr::get($data, 'route.params'));
                    }

                    // handle target
                    if ($type = Arr::get($data, 'type')) {
                        if ($typeConfig = config('nova-editor-js.toolSettings.link.config.types.' . $type)) {
                            if (!empty($typeConfig['target'])) {
                                $xml->addAttribute('target', (string)$typeConfig['target']);
                            }
                            if (!empty($typeConfig['rel'])) {
                                $xml->addAttribute('rel', (string)$typeConfig['rel']);
                            }
                        }
                    }

                    unset($attrs['data-link']);

                    $link = trim(preg_replace('~^<\?xml.*?\?>~', '', $xml->saveXML(), 1));
                    // echo "<xmp>" . nl2br($link) . "</xmp>";
                }
            }
        } catch (\Exception $e) {
            $link = strip_tags($link);
            report($e);
        } finally {
            return $link;
        }
    }
}
