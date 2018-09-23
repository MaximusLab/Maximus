<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo Tsun <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Composer;

use Composer\Script\Event;

class ScriptHandler
{
    /**
     * Generate default configuration "maximus.yaml" into config/packages folder.
     *
     * @param Event $event
     */
    public static function copyConfiguration(Event $event)
    {
        $target = 'config/packages/maximus.yaml';
        $source = 'config/packages/maximus.yaml.dist';

        if (!file_exists($target) && file_exists($source)) {
            if (!copy($source, $target)) {
                $event->getIO()->write('');
                $event->getIO()->write('<fg=white;bg=red>                                       </>');
                $event->getIO()->write('<fg=white;bg=red>  [ERROR] Copy "maximus.yaml" failed!  </>');
                $event->getIO()->write('<fg=white;bg=red>                                       </>');
                $event->getIO()->write('');

                return;
            }

            $event->getIO()->write('');
            $event->getIO()->write('<fg=black;bg=green>                                     </>');
            $event->getIO()->write('<fg=black;bg=green>  Copy "maximus.yaml" successfully!  </>');
            $event->getIO()->write('<fg=black;bg=green>                                     </>');
            $event->getIO()->write('');
        }
    }
}
