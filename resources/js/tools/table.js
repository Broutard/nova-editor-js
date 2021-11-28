NovaEditorJS.booting(function (editorConfig, fieldConfig) {
    if (fieldConfig.toolSettings.table && fieldConfig.toolSettings.table.activated === true) {
        editorConfig.tools.table = {
            class: require('@editorjs/table'),
            inlineToolbar: fieldConfig.toolSettings.table.inlineToolbar,
        }
    }
});
