<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Response;

class PlainTextResponse extends Response
{
    public function __construct(string $content = '', int $status = 200, array $headers = array())
    {
        $headers['Content-Type'] = 'text/plain';

        parent::__construct($content, $status, $headers);
    }
}
