<?php

namespace FryskeOranjekoeke\View\Partials;

use \FryskeOranjekoeke\View\View as View;

// @TODO Docs
class HtmlTags extends Partial
{
    protected $templates = [
        'css'    => '<link href="$url" rel="stylesheet" type="text/css">',
        'script' => '<script src="$url" type="text/javascript"></script>',
        'img'    => '<img src="$url"></img>'
    ];

    public function getTemplate(string $name): ?string
    {
        return ($this->templates[$name] ?? null);
    }

    public function getUrl(string $type, string $name, bool $addSuffix = true): string
    {
        $suffix = ($addSuffix) ? ('.' . $type) : '';
        $location = (strpos($name, 'vendor') !== false) ? ($name . $suffix) : ($type . DS . $name . $suffix);
        return (ASSETS . $location);
    }

    public function parseTemplate(string $template, string $url): string
    {
        return strtr($this->getTemplate($template), [
            '$url' => $url
        ]);
    }

    public function __construct(View $view)
    {
        parent::__construct($view);
    }

    public function css($name)
    {
        if (is_string($name)) {
            $name = [$name];
        }

        $output = '';
        foreach ($name as $file) {
            $url = $this->getUrl('css', $file);
            $output .= $this->parseTemplate('css', $url);
        }
        return $output;
    }

    public function script($name)
    {
        if (is_string($name)) {
            $name = [$name];
        }

        $output = '';
        foreach ($name as $file) {
            $url = $this->getUrl('js', $file);
            $output .= $this->parseTemplate('script', $url);
        }
        return $output;
    }

    public function image(string $name)
    {
        $url = $this->getUrl('img', $name, false);
        $extension = (pathinfo($url)['extension'] ?? null);
        if ($extension === null) {
            $url .= ('.png');
        }

        return $this->parseTemplate('img', $url);
    }
}
