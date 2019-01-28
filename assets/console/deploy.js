let $ = require('jquery');
let $deployButton = $('#deploy-button');
let parameters = {};
let generating = false;

function generateHtmlFiles()
{
    if (generating) {
        if (parameters.htmlUrls.length) {
            setTimeout(generateHtmlFiles, 1000);
        }
        return;
    }

    generating = true;

    let url = parameters.htmlUrls.shift();

    $.get(url, function(html) {
        $.post(parameters.generateFileUrl, {url:url, html:html}, function(response) {
            generating = false;

            if (!parameters.htmlUrls.length) {
                $.post(parameters.pushUrl, function() {
                    // Display complete messages
                    $('#deploying-content').addClass('d-none');
                    $('#after-deploy-content').removeClass('d-none');
                    $('#close-button').removeClass('d-none');
                });
            }
        }, 'json');
    });

    if (parameters.htmlUrls.length > 0) {
        setTimeout(generateHtmlFiles, 1000);
    }
}

$deployButton.on('click', function () {
    let parameterUrl = $deployButton.data('parameter-url');

    $('#before-deploy-content').addClass('d-none');
    $('#deploying-content').removeClass('d-none');
    $('#close-button').addClass('d-none');

    $.getJSON(parameterUrl, function(response) {
        parameters = response;
        $.post(parameters.prepareGitRepoUrl, function(response) {
            if (response.success) {
                $.post(parameters.deleteAssetUrl, function(response) {
                    if (response.success) {
                        $.post(parameters.copyAssetUrl, function(response) {
                            if (response.success) {
                                generateHtmlFiles();
                            }
                        });
                    }
                });
            }
        }, 'json');
    });
});
