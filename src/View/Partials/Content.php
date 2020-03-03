<?php

namespace FryskeOranjekoeke\View\Partials;

use \FryskeOranjekoeke\View\View as View;

class Content extends Partial
{
    protected $templates = [
        'placeholder_content_block' => '{{PlaceholderBlock::$name}}',
        'start_content_block'       => '{{StartBlock::$name}}',
        'end_content_block'         => '{{EndBlock::$name}}'
    ];

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

    public function __construct(View $view)
    {
        parent::__construct($view);
    }

    public function placeholderContentBlock(string $name): string
    {
        return $this->parseTemplate('placeholder_content_block', $name);
    }

    public function startContentBlock(string $name): string
    {
        return $this->parseTemplate('start_content_block', $name);
    }

    public function endContentBlock(string $name): string
    {
        return $this->parseTemplate('end_content_block', $name);
    }

    public function parseContentBlocks(string $layout, string $view): string
    {
        $matches = [];
        // Build regex
        preg_match_all('/' . $this->placeholderContentBlock('(.*?)') . '/', $layout, $matches);
        if (empty($matches)) {
            return ($layout . $view);
        }

        $content = $layout;
        // 1 bevat de results
        foreach (($matches[1] ?? []) as $key => $placeholder) {
            // Get the content by the placeholders from the view.
            $str = get_string_between($view, $this->startContentBlock($placeholder), $this->endContentBlock($placeholder));
            $content = str_replace($this->placeholderContentBlock($placeholder), $str, $content);
        }
        return $content;
    }

    public function include(string $name)
    {
        $filepath = VIEWS . DS . 'Includes' . DS . $name . '.php';
        return $this->view->getRequireContents($filepath);
    }
}
