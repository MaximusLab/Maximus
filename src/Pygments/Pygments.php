<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Pygments;

use Symfony\Component\Process\Process;

/**
 * A PHP wrapper for Pygments, the Python syntax highlighter
 */
class Pygments
{
    /**
     * @var string
     */
    private $pygmentize;

    /**
     * Constructor
     *
     * @param string $pygmentize The path to pygmentize command
     */
    public function __construct($pygmentize = 'pygmentize')
    {
        $this->pygmentize = $pygmentize;
    }

    /**
     * Highlight the input code
     *
     * @param string $code      The code to highlight
     * @param string $lexer     The name of the lexer (php, html,...)
     * @param string $formatter The name of the formatter (html, ansi,...)
     * @param array  $options   An array of options
     *
     * @return string
     */
    public function highlight($code, $lexer = null, $formatter = null, $options = [])
    {
        $command = [$this->pygmentize];

        if ($lexer) {
            $command[] = '-l';
            $command[] = $lexer;

            if (false !== strpos($lexer, ':')) {
                $command[] = '-x';
            }
        } else {
            $command[] = '-g';
        }

        if ($formatter) {
            $command[] = '-f';
            $command[] = $formatter;
        }

        if (count($options)) {
            foreach ($options as $key => $value) {
                $command[] = '-P';
                $command[] = sprintf('%s=%s', $key, $value);
            }
        }

        return $this->getOutput(new Process($command, null, null, $code));
    }

    /**
     * Gets style definition
     *
     * @param string $style    The name of the style (default, colorful,...)
     * @param string $selector The css selector
     *
     * @return string
     */
    public function getCss($style = 'default', $selector = null)
    {
        $command = [$this->pygmentize];
        $command[] = '-f';
        $command[] = 'html';
        $command[] = '-S';
        $command[] = $style;

        if ($selector) {
            $command[] = '-a';
            $command[] = $selector;
        }

        return $this->getOutput(new Process($command));
    }

    /**
     * Guesses a lexer name based solely on the given filename
     *
     * @param string $fileName The file does not need to exist, or be readable.
     *
     * @return string
     */
    public function guessLexer($fileName)
    {
        $command = [$this->pygmentize];
        $command[] = '-N';
        $command[] = $fileName;

        return trim($this->getOutput(new Process($command)));
    }

    /**
     * Gets a list of lexers
     *
     * @return array
     */
    public function getLexers()
    {
        $command = [$this->pygmentize];
        $command[] = '-L';
        $command[] = 'lexer';

        $output = $this->getOutput(new Process($command));

        return $this->parseList($output);
    }

    /**
     * Gets a lexer from file
     *
     * @param string $filename Lexer filename (e.g. terminal.py)
     * @param string $lexer    Lexer Name (e.g. terminal)
     *
     * @return string
     */
    public function getLexerFromFile($filename, $lexer)
    {
        $filePath = __DIR__.'/lexers/'.$filename;

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException('Lexer file "'.$filename.'" is not exists!');
        }

        $filePath = str_replace('\\', '/', realpath($filePath));
        $lexerMethod = ucfirst($lexer).'Lexer';

        return $filePath.':'.$lexerMethod;
    }

    /**
     * Gets a list of formatters
     *
     * @return array
     */
    public function getFormatters()
    {
        $command = [$this->pygmentize];
        $command[] = '-L';
        $command[] = 'formatter';

        $output = $this->getOutput(new Process($command));

        return $this->parseList($output);
    }

    /**
     * Gets a list of styles
     *
     * @return array
     */
    public function getStyles()
    {
        $command = [$this->pygmentize];
        $command[] = '-L';
        $command[] = 'style';

        $output = $this->getOutput(new Process($command));

        return $this->parseList($output);
    }

    /**
     * @param Process $process
     * @throws \RuntimeException
     * @return string
     */
    protected function getOutput(Process $process)
    {
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    /**
     * @param string $input
     * @return array
     */
    protected function parseList($input)
    {
        $list = [];

        if (preg_match_all('/^\* (.*?):\r?\n *([^\r\n]*?)$/m', $input, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $names = explode(',', $match[1]);

                foreach ($names as $name) {
                    $list[trim($name)] = $match[2];
                }
            }
        }

        return $list;
    }
}
