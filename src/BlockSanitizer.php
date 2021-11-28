<?php

namespace Broutard\NovaEditorJs;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BlockSanitizer
{
    public function handleLinks($block)
    {
        // Sanitize links
        if ($text = Arr::get($block, 'data.text')) {
            $text = preg_replace_callback(
                '~<a .*?>.*?</a>~is',
                function ($matches) {
                    return $this->sanitizeLink($matches[0]);
                },
                $text
            );
            Arr::set($block, 'data.text', $text);
        }

        return $block;
    }

    public function handleImage($block)
    {
        Arr::forget($block, 'data.file.url');

        return $block;
    }

    /**
     * Transform self domain link to the corresponding link type
     * @param $link
     * @return string
     */
    protected function sanitizeLink($link)
    {
        if ($xml = new \SimpleXMLElement($link)) {
            $attrs = $xml->attributes();

            if (!empty($attrs['data-link'])) {
                $data = json_decode($attrs['data-link'], true);
            } else {
                $data = [];
            }

            if ($href = (string)$attrs->href) {
                // handle bad internal links (copied as external)
                if ($this->relativizeAppUrl($href)) {
                    $attrs->href = $href;

                    // try to set data-link attribute
                    try {
                        if ($route = app('router')->getRoutes()->match(app('request')->create($href))) {
                            $data = array_merge($data, [
                                'route' => [
                                    'name'   => $route->getName(),
                                    'params' => $route->parameters(),
                                ],
                            ]);

                            $action = $route->getAction();

                            if (isset($action['controller'])
                                && is_string($action['controller'])
                                && strpos($action['controller'], '@') !== false
                            ) {
                                [$controller, $method] = explode('@', $action['controller']);
                                $reflector = new \ReflectionMethod($controller, $method);
                            } elseif (isset($action['uses']) && $action['uses'] instanceof \Closure) {
                                $reflector = new \ReflectionFunction($action['uses']);
                            }

                            if (isset($reflector)) {
                                foreach ($reflector->getParameters() as $param) {
                                    if ($param->hasType()) {
                                        $type = $param->getType()->getName();
                                        $instance = new $type;
                                        if (is_a($instance, \Illuminate\Database\Eloquent\Model::class)) {
                                            $model = $instance->getMorphClass();
                                            $data['model'] = $model;
                                            $data['id'] = (int)$route->parameter($model);
                                        }
                                    }
                                }
                            }

                            $attrs['data-link'] = json_encode($data);
                        }
                    } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
                    }

                    $link = trim(explode("\n", $xml->asXML(), 2)[1]);
                }
            }
        }

        return $link;
    }

    /**
     * Try to relativize url (for internal url)
     * @return bool
     */
    protected function relativizeAppUrl(&$url): bool
    {
        $url = preg_replace('~^(https?:)?//~i', '', $url);
        $appUrl = preg_replace('~^(https?:)?//~i', '', config('app.url'));

        if (Str::startsWith($url, $appUrl)) {
            $url = Str::after($url, $appUrl);
            return true;
        }

        return false;
    }
}
