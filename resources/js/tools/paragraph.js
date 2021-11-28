NovaEditorJS.booting(function (editorConfig, fieldConfig) {
    editorConfig.tools.paragraph = {
        class: require('@editorjs/paragraph'),
        inlineToolbar: fieldConfig.toolSettings.paragraph?.inlineToolbar || true
    }
});
