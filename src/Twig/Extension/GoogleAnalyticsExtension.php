<?php
/*
 * This file is part of the Maximus package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maximus\Twig\Extension;

use Maximus\Setting\Settings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GoogleAnalyticsExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $trackingId;

    /**
     * @var string
     */
    private $scripts;

    /**
     * GoogleAnalyticsExtension constructor.
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->trackingId = $settings->getGaTrackingId();
        $this->scripts = $settings->getGaTrackingScripts();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('google_analytics', [$this, 'showGoogleAnalyticsScripts'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Show ga embed javascript
     *
     * @return string
     */
    public function showGoogleAnalyticsScripts()
    {
        if (empty($this->trackingId)) {
            return '';
        }

        $html = <<<HTML
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$this->trackingId}" type="text/javascript"></script>
<script type="text/javascript">
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '{$this->trackingId}');
  
  {$this->scripts}
</script>
HTML;
        return $html;
    }
}
