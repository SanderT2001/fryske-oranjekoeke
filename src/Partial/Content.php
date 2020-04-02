<?php

namespace FryskeOranjekoeke\Partial;

class Content extends Partial
{
    protected $templates = [
        'placeholder_content_block' => '{{PlaceholderBlock::$name}}',
        'start_content_block'       => '{{StartBlock::$name}}',
        'end_content_block'         => '{{EndBlock::$name}}'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function getTemplate(string $name): ?string
    {
        return ($this->templates[$name] ?? null);
    }

    public function parseTemplate(string $template, string $name): string
    {
        return strtr($this->getTemplate($template), [
            '$name' => $name
        ]);
    }

    public function placeholder(string $name): string
    {
        return $this->parseTemplate('placeholder_content_block', $name);
    }

    public function start(string $name): string
    {
        return $this->parseTemplate('start_content_block', $name);
    }

    public function end(string $name): string
    {
        return $this->parseTemplate('end_content_block', $name);
    }

    public function parseContentBlocks(string $layout, string $view): string
    {
        $matches = [];
        // Build regex
        preg_match_all('/' . $this->placeholder('(.*?)') . '/', $layout, $matches);
        if (empty($matches)) {
            return ($layout . $view);
        }

        $content = $layout;
        // 1 bevat de results
        foreach (($matches[1] ?? []) as $key => $placeholder) {
            // Get the content by the placeholders from the view.
            $str = get_string_between($view, $this->start($placeholder), $this->end($placeholder));
            $content = str_replace($this->placeholder($placeholder), $str, $content);
        }
        return $content;
    }
}
