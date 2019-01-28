require('codemirror/lib/codemirror.css');
require('codemirror/theme/monokai.css');

let CodeMirror = require('codemirror');
let CodeMirrorModeJavascript = require('codemirror/mode/javascript/javascript');
let beautify = require('js-beautify').js;

let themeVariables = document.getElementById('settings_themeVariables');
let themeMenus = document.getElementById('settings_themeMenus');
let gaTrackingScripts = document.getElementById('settings_gaTrackingScripts');
let excludeAssets = document.getElementById('settings_excludeAssets');

themeVariables.value = beautify(themeVariables.value, { indent_size: 2, space_in_empty_paren: true });
excludeAssets.value = beautify(excludeAssets.value, { indent_size: 2, space_in_empty_paren: true });

if (gaTrackingScripts.value === '') {
    gaTrackingScripts.value = "\n\n";
}

CodeMirror.fromTextArea(
    themeVariables,
    {
        mode: {
            name: 'javascript',
            json: true,
            statementIndent: 2
        },
        theme: 'monokai'
    }
);

CodeMirror.fromTextArea(
    themeMenus,
    {
        mode: {
            name: 'javascript',
            json: true,
            statementIndent: 2
        },
        theme: 'monokai'
    }
);

CodeMirror.fromTextArea(
    gaTrackingScripts,
    {
        mode: {
            name: 'javascript',
            statementIndent: 2
        },
        theme: 'monokai'
    }
);

CodeMirror.fromTextArea(
    excludeAssets,
    {
        mode: {
            name: 'javascript',
            json: true,
            statementIndent: 2
        },
        theme: 'monokai'
    }
);
