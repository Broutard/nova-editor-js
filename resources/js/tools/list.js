NovaEditorJS.booting(function (editorConfig, fieldConfig) {
    if (fieldConfig.toolSettings.list && fieldConfig.toolSettings.list.activated === true) {
        editorConfig.tools.list = {
            class: require('@editorjs/list'),
            inlineToolbar: fieldConfig.toolSettings.list.inlineToolbar,
            shortcut: fieldConfig.toolSettings.list.shortcut
        }
    }
});
