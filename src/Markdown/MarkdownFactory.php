<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Markdown;

use Maximus\Pygments\Pygments;
use Michelf\MarkdownExtra;

/**
 * Class Parser
 *
 * @package Maximus\Markdown
 */
class MarkdownFactory
{
    /**
     * @return MarkdownExtra
     */
    public function createMarkdownParser()
    {
        $parser = new MarkdownExtra();

        $parser->code_attr_on_pre = true;
        $parser->code_block_content_func = function($code, $language) {
            if (empty($language)) {
                $language = 'text';
            }

            $pygments = new Pygments();

            return $pygments->highlight($code, $language, 'html', ['encoding' => 'utf-8', 'startinline' => true]);
        };

        return $parser;
    }
}
