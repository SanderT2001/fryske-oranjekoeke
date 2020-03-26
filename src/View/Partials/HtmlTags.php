<?php

namespace FryskeOranjekoeke\View\Partials;

use \FryskeOranjekoeke\View\View as View;

// @TODO Docs
class HtmlTags extends Partial
{
    protected $templates = [
        'css'    => '<link href="$url" rel="stylesheet" type="text/css" $attributes>',
        'script' => '<script src="$url" type="text/javascript" $attributes></script>',
        'img'    => '<img src="$url" $attributes></img>',
        'attr'   => '$name="$value"'
    ];

    public function getTemplate(string $name): ?string
    {
        return ($this->templates[$name] ?? null);
    }

    public function getUrl(string $type, string $name, bool $addSuffix = true): string
    {
        if ($type === 'img') {
            $extension = (pathinfo($name)['extension'] ?? null);
            if ($extension === null) {
                $name .= ('.png');
            }
        }

        $suffix = ($addSuffix) ? ('.' . $type) : '';
        $location = (strpos($name, 'vendor') !== false) ? ($name . $suffix) : ($type . DS . $name . $suffix);
        return (ASSETS . $location);
    }

    public function prepareAttributes(array $attributes): string
    {
        // @TODO (Sander) eigenlijk moet parseTemplate dit doen.
        $template = $this->getTemplate('attr');

        $output = '';
        foreach ($attributes as $name => $attr) {
            $output .= strtr($template, [
                '$name'  => $name,
                '$value' => $attr
            ]);
        }
        return $output;
    }

    public function parseTemplate(string $template, string $url, string $attributes = ''): string
    {
        return strtr($this->getTemplate($template), [
            '$url'        => $url,
            '$attributes' => $attributes
        ]);
    }

    public function __construct(View $view)
    {
        parent::__construct($view);
    }

    public function css($name, array $attributes = [])
    {
        if (is_string($name)) {
            $name = [$name];
        }

        $attributes = $this->prepareAttributes($attributes);

        $output = '';
        foreach ($name as $file) {
            $url = $this->getUrl('css', $file);
            $output .= $this->parseTemplate('css', $url, $attributes);
        }
        return $output;
    }

    public function script($name, array $attributes = [])
    {
        if (is_string($name)) {
            $name = [$name];
        }

        $attributes = $this->prepareAttributes($attributes);

        $output = '';
        foreach ($name as $file) {
            $url = $this->getUrl('js', $file);
            $output .= $this->parseTemplate('script', $url, $attributes);
        }
        return $output;
    }

    public function image(string $name, array $attributes = [])
    {
        $url        = $this->getUrl('img', $name, false);
        $attributes = $this->prepareAttributes($attributes);
        return $this->parseTemplate('img', $url, $attributes);
    }
}
