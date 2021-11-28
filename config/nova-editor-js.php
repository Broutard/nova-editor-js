<?php

return [
    /**
     * Editor settings
     */
    'editorSettings' => [
        'logLevel' => env('APP_DEBUG') ? 'VERBOSE' : 'ERROR',
        'placeholder' => '',
        'defaultBlock' => 'paragraph',
        'autofocus' => false,
    ],

    /**
     * Configure tools
     */
    'toolSettings' => [
        'header' => [
            'activated' => true,
            'placeholder' => 'Heading',
            'shortcut' => 'CMD+SHIFT+H'
        ],
        'list' => [
            'activated' => true,
            'inlineToolbar' => true,
            'shortcut' => 'CMD+SHIFT+L'
        ],
        'code' => [
            'activated' => true,
            'placeholder' => '',
            'shortcut' => 'CMD+SHIFT+C'
        ],
        'image' => [
            'activated' => true,
            'shortcut' => 'CMD+SHIFT+I',
            'path' => 'tmp',
            'disk' => 'public',
        ],
        'marker' => [
            'activated' => true,
            'shortcut' => 'CMD+SHIFT+M',
        ],
        'delimiter' => [
            'activated' => true,
        ],
        'table' => [
            'activated' => true,
            'inlineToolbar' => true,
        ],
        'raw' => [
            'activated' => true,
            'placeholder' => '',
        ],
        'embed' => [
            'activated' => true,
            'inlineToolbar' => true,
            'services' => [
                'codepen' => false,
                'imgur' => false,
                'vimeo' => false,
                'youtube' => true,
                'dailymotion' => true,
                'twitter' => false,
                'instagram' => false,
                'facebook' => false,
            ],
        ],
        'quote' => [
            'activated' => true,
        ],
    ],

    /**
     * Input validation config
     * https://github.com/editor-js/editorjs-php
     */
    'validationSettings' => [
        'tools' => [
            'header' => [
                'text' => [
                    'type' => 'string',
                ],
                'level' => [
                    'type' => 'int',
                    'canBeOnly' => [1, 2, 3, 4, 5]
                ]
            ],
            'paragraph' => [
                'text' => [
                    'type' => 'string',
                ]
            ],
            'list' => [
                'style' => [
                    'type' => 'string',
                    'canBeOnly' =>
                        [
                            0 => 'ordered',
                            1 => 'unordered',
                        ],
                ],
                'items' => [
                    'type' => 'array',
                    'data' => [
                        '-' => [
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            'image' => [
                'file' => [
                    'type' => 'array',
                    'data' => [
                        'path' => [
                            'type' => 'string',
                        ],
                        'url' => [
                            'type' => 'string',
                        ],
                    ],
                ],
                'caption' => [
                    'type' => 'string'
                ],
                'withBorder' => [
                    'type' => 'boolean'
                ],
                'withBackground' => [
                    'type' => 'boolean'
                ],
                'stretched' => [
                    'type' => 'boolean'
                ]
            ],
            'code' => [
                'code' => [
                    'type' => 'string'
                ]
            ],
            'delimiter' => [

            ],
            'table' => [
                'content' => [
                    'type' => 'array',
                    'data' => [
                        '-' => [
                            'type' => 'array',
                            'data' => [
                                '-' => [
                                    'type' => 'string',
                                ]
                            ]
                        ]
                    ]
                ],
                'withHeadings' => [
                    'type' => 'boolean'
                ],
            ],
            'raw' => [
                'html' => [
                    'type' => 'string',
                ]
            ],
            'embed' => [
                'service' => [
                    'type' => 'string'
                ],
                'source' => [
                    'type' => 'string'
                ],
                'embed' => [
                    'type' => 'string'
                ],
                'width' => [
                    'type' => 'int'
                ],
                'height' => [
                    'type' => 'int'
                ],
                'caption' => [
                    'type' => 'string',
                    'required' => false,
                ],
            ],
            'quote' => [
                'text' => [
                    'type' => 'string'
                ],
                'caption' => [
                    'type' => 'string'
                ],
                'alignment' => [
                    'type' => 'string'
                ],
            ]
        ],
    ]
];
