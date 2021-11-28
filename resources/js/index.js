// Import the Nova Editor class
import NovaEditorJS from "./nova-editor";

// Expose it for other plugins
window.NovaEditorJS = new NovaEditorJS();

// Import tools
require('./tools/code');
require('./tools/delimiter');
require('./tools/embed');
require('./tools/heading');
require('./tools/image');
require('./tools/inline-code');
require('./tools/list');
require('./tools/marker');
require('./tools/paragraph');
require('./tools/raw');
require('./tools/table');
require('./tools/quote');

// Import the Nova field declaration
require('./field');
