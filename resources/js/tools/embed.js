NovaEditorJS.booting(function (editorConfig, fieldConfig) {
    if (fieldConfig.toolSettings.embed && fieldConfig.toolSettings.embed.activated === true) {
        let services = _.mapValues(fieldConfig.toolSettings.embed.services, function(service, key) {
            // handle service config from PHP json_encode
            if (typeof service === 'object') {
                if (typeof service.regex === 'string') {
                    service.regex = new RegExp(service.regex);
                }
                if (typeof service.id === 'string') {
                    service.id = eval(service.id);
                }
            }
            // add custom usefull services
            else if (service === true) {
                if (key === 'dailymotion') {
                    service = {
                        regex: /https?:\/\/([^\/\?\&]*).dailymotion.com\/video\/([^\/\?\&]*)\/?$/,
                        embedUrl: 'https://www.dailymotion.com/embed/video/<%= remote_id %>',
                        html: "<iframe scrolling='no' frameborder='no' allowtransparency='true' allowfullscreen='true' style='width: 100%; height: 400px; max-height: 1000px;'></iframe>",
                        id: (ids) => {
                            return ids[1];
                        }
                    }
                }
                else if (key === 'pinterest') {
                    service = {
                        regex: /https?:\/\/([^\/\?\&]*).pinterest.com\/pin\/([^\/\?\&]*)\/?$/,
                        embedUrl: 'https://assets.pinterest.com/ext/embed.html?id=<%= remote_id %>',
                        html: "<iframe scrolling='no' frameborder='no' allowtransparency='true' allowfullscreen='true' style='width: 100%; min-height: 400px; max-height: 1000px;'></iframe>",
                        id: (ids) => {
                            return ids[1];
                        }
                    }
                }
            }
            return service
        })

        editorConfig.tools.embed = {
            class: require('@editorjs/embed'),
            inlineToolbar: fieldConfig.toolSettings.embed.inlineToolbar,
            config: {
                services
            }
        }
        // console.log(editorConfig.tools.embed.config.services)
    }
});
