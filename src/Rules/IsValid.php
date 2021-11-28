<?php

namespace Broutard\NovaEditorJs\Rules;

// use EditorJS\EditorJS;
// use EditorJS\EditorJSException;
use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Broutard\NovaEditorJs\EditorJs;
use Broutard\NovaEditorJs\Exceptions\EditorJSException;

class IsValid implements Rule
{
    const DEFAULT_ARRAY_KEY = "-";

    protected $message;

    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return true;
        }

        // Initialize Editor backend and validate structure
        $validation = $this->validate($value);

        if ($validation !== true) {
            $this->message = collect((array)$validation->getMessages())->map(function ($messages, $key) {
                return $key . ':<br>' . implode('<br>', $messages);
            })->implode('<br>');
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }

    protected function validate($value)
    {
        $editor = new EditorJs($value);

        $errors = new MessageBag;

        $editor->getBlocks()->each(function ($block, $i) use ($errors) {
            $i++;

            if (!$type = Arr::get($block, 'type')) {
                $errors->add('block' . $i, 'Missing type for block ' . $i);
            } elseif (!$data = Arr::get($block, 'data')) {
                $errors->add('block' . $i, 'Missing data for block ' . $i);
            } else {
                try {
                    $this->validateBlock($type, $data);
                } catch (EditorJSException $e) {
                    $errors->add('block' . $i, $e->getMessage());
                }
            }
        });

        return $errors->isEmpty() ? true : $errors;
    }

    /**
     * Validate block
     *
     * @param string $blockType
     * @param array $blockData
     *
     * @return bool
     * @throws EditorJSException
     *
     */
    protected function validateBlock($blockType, $blockData, $rules = null)
    {
        $validateConfig = config('nova-editor-js.validationSettings');

        /**
         * No rules configured => no validation
         */
        if (is_null($rules)) {
            if (!$rules = Arr::get($validateConfig, 'tools.' . $blockType)) {
                return true;
            }
        }

        /**
         * Make sure that every required param exists in data block
         */
        foreach ($rules as $key => $value) {
            if (($key != self::DEFAULT_ARRAY_KEY) && (isset($value['required']) ? $value['required'] : true)) {
                if (!isset($blockData[$key])) {
                    throw new EditorJSException("Not found required param `$key`");
                }
            }
        }

        /**
         * Check if there is not extra params (not mentioned in configuration rule)
         */
        foreach ($blockData as $key => $value) {
            if (!is_integer($key) && !isset($rules[$key])) {
                throw new EditorJSException("Found extra param `$key`");
            }
        }

        /**
         * Validate every key in data block
         */
        foreach ($blockData as $key => $value) {
            /**
             * PHP Array has integer keys
             */
            if (is_integer($key)) {
                $key = self::DEFAULT_ARRAY_KEY;
            }

            $rule = $rules[$key];

            $rule = $this->expandToolSettings($rule);

            $elementType = $rule['type'];

            /**
             * Process canBeOnly rule
             */
            if (isset($rule['canBeOnly'])) {
                if (!in_array($value, $rule['canBeOnly'])) {
                    throw new EditorJSException("Option '$key' with value `$value` has invalid value. Should be [" . implode(', ', $rule['canBeOnly']) . "].");
                }

                // Do not perform additional elements validation in any case
                continue;
            }

            /**
             * Do not check element type if it is not required and null
             */
            if (isset($rule['required']) && $rule['required'] === false &&
                isset($rule['allow_null']) && $rule['allow_null'] === true && $value === null) {
                continue;
            }

            /**
             * Validate element types
             */
            switch ($elementType) {
                case 'string':
                    if (!is_string($value)) {
                        throw new EditorJSException("Option '$key' with value `$value` must be string");
                    }
                    break;

                case 'integer':
                case 'int':
                    if (!is_integer($value)) {
                        throw new EditorJSException("Option '$key' with value `$value` must be integer");
                    }
                    break;

                case 'array':
                    $this->validateBlock($blockType, $value, $rule['data']);
                    break;

                case 'boolean':
                case 'bool':
                    if (!is_bool($value)) {
                        throw new EditorJSException("Option '$key' with value `$value` must be boolean");
                    }
                    break;

                default:
                    throw new EditorJSException("Unhandled type `$elementType`");
            }
        }

        return true;
    }

    /**
     * Expand shortified tool settings
     *
     * @param $rule – tool settings
     *
     * @return array – expanded tool settings
     * @throws EditorJSException
     *
     */
    private function expandToolSettings($rule)
    {
        if (is_string($rule)) {
            // 'blockName': 'string' – tool with string type and default settings
            $expandedRule = ["type" => $rule];
        } elseif (is_array($rule)) {
            if (Arr::isAssoc($rule)) {
                $expandedRule = $rule;
            } else {
                // 'blockName': [] – tool with canBeOnly and default settings
                $expandedRule = ["type" => "string", "canBeOnly" => $rule];
            }
        } else {
            throw new EditorJSException("Cannot determine element type of the rule `$rule`.");
        }

        return $expandedRule;
    }
}
