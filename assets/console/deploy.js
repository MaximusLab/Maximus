let $ = require('jquery');
let $generate = $('#generate');
let data = $generate.data('data');
let generating = false;

function generateHtmlFiles()
{
    if (generating) {
        if (data.urls.length) {
            setTimeout(generateHtmlFiles, 1000);
        }
        return;
    }

    generating = true;

    let url = data.urls.shift();

    $.get(url, function(html) {
        $.post(data.generateFileUrl, {url:url, html:html}, function() {
            generating = false;

            if (!data.urls.length) {
                alert('Generating Done!');
            }
        });
    });

    setTimeout(generateHtmlFiles, 1000);
}

$generate.on('click', function () {
    $.post(data.copyAssetUrl, function() {
        generateHtmlFiles();
    });
});
