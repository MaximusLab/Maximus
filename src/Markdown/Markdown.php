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
    public $table_align_class_tmpl = 'text-%%';

    /**
     * @var Pygments
     */
    private $pygments;

    /**
     * {@inheritdoc}
     */
    public function __construct(Pygments $pygments)
    {
        parent::__construct();

        $this->pygments = $pygments;

        $this->initialCodeBlockContentFunc();
    }

    /**
     * {@inheritdoc}
     */
    protected function doFencedCodeBlocks($text)
    {
        $text = preg_replace_callback('{
				(?:\n|\A)
				# 1: Opening marker
				(
					(?:~{3,}|`{3,}|:{3,}) # 3 or more tildes/backticks.
				)
				[ ]*
				(?:
					\.?([-_:a-zA-Z0-9]+) # 2: standalone class name
				)?
				[ ]*
				(?:
					' . $this->id_class_attr_catch_re . ' # 3: Extra attributes
				)?
				[ ]* \n # Whitespace and newline following marker.

				# 4: Content
				(
					(?>
						(?!\1 [ ]* \n)	# Not a closing marker.
						.*\n+
					)+
				)

				# Closing marker.
				\1 [ ]* (?= \n )
			}xm',
            [$this, '_doFencedCodeBlocks_callback'],
            $text
        );

        return $text;
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
            $codeBlock = call_user_func($this->code_block_content_func, $codeBlock, $className, $attrs);
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

        return "\n" . $this->hashBlock("<blockquote>\n$bq\n</blockquote>") . "\n\n";
    }

    /**
     * Initial code_block_content_func property
     *
     * @return void
     */
    private function initialCodeBlockContentFunc()
    {
        $this->code_block_content_func = function($code, $language, $attrs) {
            $options = [
                'encoding' => 'utf-8',
                'startinline' => true,
                'linenos' => 1,
            ];

            if (empty($language)) {
                $language = 'text';

                unset($options['linenos']);
            }

            // TODO: add caution, version-added
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

                case 'see-also':
                    $code = $this->runBlockGamut($code);
                    $title = <<<ICON
<div class="see-also-title">
    <span class="fa-stack fa-md">
      <i class="fa fa-circle fa-stack-2x"></i>
      <i class="fa fa-share fa-stack-1x fa-inverse"></i>
    </span>
    <span class="text">See Also</span>
</div>
ICON;
                    $code = $title.$code;

                    return $this->hashBlock($code);

                case 'sidebar':
                    $code = $this->runBlockGamut($code);

                    return $this->hashBlock($code);

                case 'terminal':
                    $lexer = $this->pygments->getLexerFromFile('terminal.py', 'terminal');

                    return $this->pygments->highlight($code, $lexer, 'html', $options);

                case 'config':
                    $code = $this->runBlockGamut($code);
                    $id = 'config-'.md5($code);
                    $attrs = empty($attrs) ? '.annotation .yaml .xml .php' : $attrs;
                    $navItems = [];

                    foreach (explode(' ', $attrs) as $index => $configClassName) {
                        $configNameTitle = ucfirst(strtolower(trim($configClassName, '. ')));
                        $configId = $id.'-'.$index;
                        $active = $index === 0 ? 'active' : '';
                        $navItems[] = <<<NAVITEM
<li class="nav-item">
    <a class="nav-link {$active}" data-toggle="tab" href="#{$configId}" role="tab">
        {$configNameTitle}
    </a>
</li>
NAVITEM;
                    }

                    $navItemsHTML = '<ul class="nav nav-tabs" role="tablist">'.implode('', $navItems).'</ul>';
                    $code = <<<HTML
<div id="$id" class="config">
    {$navItemsHTML}
    <div class="tab-content">
        {$code}
    </div>
</div>
HTML;

                    return $this->hashBlock($code);
            }

            return $this->pygments->highlight($code, $language, 'html', $options);
        };
    }
}
