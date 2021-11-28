NovaEditorJS.booting(function (editorConfig, fieldConfig) {
    if (fieldConfig.toolSettings.quote && fieldConfig.toolSettings.quote.activated === true) {
        editorConfig.tools.quote = {
            class: require('@editorjs/quote'),
        }
    }
});
