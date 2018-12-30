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

class DisqusExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $shortName;

    /**
     * DisqusExtension constructor.
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->shortName = $settings->getDisqusShortName();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('disqus', [$this, 'showDisqusScripts'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Show Disqus embed javascript
     *
     * @return string
     */
    public function showDisqusScripts()
    {
        if (empty($this->shortName)) {
            return '';
        }

        $html = <<<HTML
<div id="disqus_thread"></div>
<script type="text/javascript">
    /**
     *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
     *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables
     */
    /*
    var disqus_config = function () {
        this.page.url = PAGE_URL;  // Replace PAGE_URL with your page's canonical URL variable
        this.page.identifier = PAGE_IDENTIFIER; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
    };
    */
    (function() {  // DON'T EDIT BELOW THIS LINE
        var d = document, s = d.createElement('script');
        
        s.src = 'https://{$this->shortName}.disqus.com/embed.js';
        
        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>
HTML;
        return $html;
    }
}
