<?php

// @TODO Er moet een URLBUILDER komen die adhv URL of array URL kan opbouwen.

namespace \FryskeOranjekoeke\View\Partials;

use \FryskeOranjekoeke\View\View as View;

class HtmlTags extends Partial
{
    protected $templates = [
        'css' => '<link href="$url" ref="stylesheet" type="text/css">'
    ];


    public function getTemplate(string $name): ?string
    {
        return ($this->templates[$name] ?? null);
    }

    public function parseTemplate(string $template, string $name): string
    {
        return strtr($this->getTemplate($template), [
            '$url' => $name
        ]);
    }

    public function __construct(View $view)
    {
        parent::__construct($view);
    }

    public function css(string $url, bool $addToHead = true)
    {
        $tag = $this->parseTemplate('css', $url);
        if (!$addToHead) {
            return $tag;
        }

        // Add tag to head.
        if (strpos($this->view->) !== false) {
        }
    }
}
