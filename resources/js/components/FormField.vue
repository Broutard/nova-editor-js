<template>
    <default-field @keydown.native.stop :field="field" :errors="errors" :fullWidthContent="true">
        <template slot="field">
            <div :id="'editor-js-' + this.field.attribute" class="editor-js editor-js-content"></div>
        </template>
    </default-field>
</template>

<script>
    import {FormField, HandlesValidationErrors} from 'laravel-nova';
    import DragDrop from 'editorjs-drag-drop';

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ['resourceName', 'resourceId', 'field'],

        methods: {
            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {

                this.value = this.field.value;

                let self = this;
                let currentContent = (self.field.value ? JSON.parse(self.field.value) : self.field.value);

                let config = _.merge({}, {
                    /**
                     * This Tool will be used as default
                     */
                    defaultBlock: 'paragraph',

                    /**
                     * Default placeholder
                     */
                    placeholder: false,

                    /**
                     * Enable autofocus
                     */
                    autofocus: false,

                    /**
                     * Min height of editor
                     */
                    minHeight: 35,
                }, self.field.editorSettings, {
                    /**
                     * Wrapper of Editor
                     */
                    holder: 'editor-js-' + self.field.attribute,

                    /**
                     * Initial Editor data
                     */
                    data: currentContent,

                    onReady: function () {
                        new DragDrop(self.editor);
                    },
                    onChange: function (api /*, block */) {
                        // self.editor.save().then((savedData) => {
                        //     self.handleChange(savedData)
                        // });

                        // https://github.com/codex-team/editor.js/issues/1755
                        setTimeout(() => {
                          api.saver.save().then(
                            (editorData) => self.handleChange(editorData)
                          );
                        }, 200);
                    }
                });

                this.editor = NovaEditorJS.getInstance(config, self.field);
            },

            isEmpty() {
                for (let i=0; i<this.editor.blocks.getBlocksCount(); i++) {
                    if (!this.editor.blocks.getBlockByIndex(i).isEmpty) {
                        return false;
                    }
                }
                return true;
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                if (this.isEmpty()) {
                    this.value = null
                }
                formData.append(this.field.attribute, this.value || '')
            },

            /**
             * Update the field's internal value.
             */
            handleChange(value) {
                this.value = JSON.stringify(value)
            },
        },
    }
</script>
