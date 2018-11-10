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
 * Markdown Parser
 *
 * @package Maximus\Markdown
 */
class Markdown extends MarkdownExtra
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->initialCodeBlockContentFunc();
    }

    /**
     * {@inheritdoc}
     */
    protected function _doFencedCodeBlocks_callback($matches)
    {
        $className =& $matches[2];
        $attrs     =& $matches[3];
        $codeBlock = $matches[4];
        $classes = ['code-block'];

        if ($this->code_block_content_func) {
            $codeBlock = call_user_func($this->code_block_content_func, $codeBlock, $className);
        } else {
            $codeBlock = htmlspecialchars($codeBlock, ENT_NOQUOTES);
        }

        $codeBlock = preg_replace_callback('/^\n+/', [$this, '_doFencedCodeBlocks_newlines'], $codeBlock);

        if ($className != "") {
            if ($className{0} == '.') {
                $className = substr($className, 1);
            }

            $classes[] = $this->code_class_prefix . $className;
        }

        $attributes = $this->doExtraAttributes('div', $attrs, null, $classes);
        $codeBlock  = "<div$attributes>$codeBlock</div>";

        return "\n\n".$this->hashBlock($codeBlock)."\n\n";
    }

    /**
     * {@inheritdoc}
     */
    protected function _doTable_callback($matches)
    {
        $key = parent::_doTable_callback($matches);
        $key = substr($key, 0, -1);
        $text = $this->html_hashes[$key];

        unset($this->html_hashes[$key]);

        $text = str_replace('<table>', '<table class="table table-bordered">', $text);
        $text = str_replace('<thead>', '<thead class="table-active">', $text);

        return $this->hashBlock($text) . "\n";
    }

    /**
     * {@inheritdoc}
     */
    protected function _doBlockQuotes_callback($matches)
    {
        $bq = $matches[1];
        // trim one level of quoting - trim whitespace-only lines
        $bq = preg_replace('/^[ ]*>[ ]?|^[ ]+$/m', '', $bq);
        $bq = preg_replace('/^/m', "  ", $bq);
        // These leading spaces cause problem with <pre> content,
        // so we need to fix that:
        $bq = preg_replace_callback('{(\s*<pre>.+?</pre>)}sx',
            array($this, '_doBlockQuotes_callback2'), $bq);

        $attributes = 'class="h4 text-center font-italic"';
        $hr = '<hr class="hr hr-gradient">';

        return "\n" . $this->hashBlock("$hr<p $attributes><q>\n$bq\n</q></p>$hr") . "\n\n";
    }

    /**
     * Initial code_block_content_func property
     *
     * @return void
     */
    private function initialCodeBlockContentFunc()
    {
        $this->code_block_content_func = function($code, $language) {
            $pygments = new Pygments();
            $options = [
                'encoding' => 'utf-8',
                'startinline' => true,
                'linenos' => 1,
            ];

            if (empty($language)) {
                $language = 'text';

                unset($options['linenos']);
            }

            switch ($language) {
                case 'note':
                    $code = $this->runBlockGamut($code);
                    $title = <<<ICON
<div class="note-title">
    <span class="fa-stack fa-md">
      <i class="fa fa-circle fa-stack-2x"></i>
      <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
    </span>
    <span class="text">Note</span>
</div>
ICON;
                    $code = $title.$code;

                    return $this->hashBlock($code);

                case 'tip':
                    $code = $this->runBlockGamut($code);
                    $title = <<<ICON
<div class="tip-title">
    <span class="fa-stack fa-md">
      <i class="fa fa-circle fa-stack-2x"></i>
      <i class="fa fa-lightbulb-o fa-stack-1x fa-inverse"></i>
    </span>
    <span class="text">Tip</span>
</div>
ICON;
                    $code = $title.$code;

                    return $this->hashBlock($code);
            }

            return $pygments->highlight($code, $language, 'html', $options);
        };
    }
}
