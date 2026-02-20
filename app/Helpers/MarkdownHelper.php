<?php

namespace App\Helpers;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use Illuminate\Support\Str;

class MarkdownHelper
{
    public static function render(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $html = $converter->convert($text)->getContent();

        // Clean up typography and ensure it's safe
        return (string) $html;
    }
}
