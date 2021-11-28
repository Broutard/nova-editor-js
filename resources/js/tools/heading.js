NovaEditorJS.booting(function (editorConfig, fieldConfig) {
    if (fieldConfig.toolSettings.header && fieldConfig.toolSettings.header.activated === true) {
        editorConfig.tools.header = {
            class: require('@editorjs/header'),
            config: {
                placeholder: fieldConfig.toolSettings.header.placeholder,
                defaultLevel: 1,
                ...fieldConfig.toolSettings.header.config
            },
            shortcut: fieldConfig.toolSettings.header.shortcut
        }
    }
});
