<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\textformats;

use Yii;
use yii\base\Behavior;

/**
 * TextFormatsBehaviour
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package
 */
class TextFormatsBehavior extends Behavior
{
    /**
     * @var array information about text formats
     * Each item is format. Key is format id, value is format description.
     * Description fields:
     * `label` - format label;
     * `formatter` - source to html Closure `string function($model, $language=null)`. If is null, copy will be used;
     * `widgetClass` - widget class for editor
     * `widgetOptions` - widget options
     */
    public $textFormats;

    /**
     * @var array additional options for widget
     */
    public $textEditorWidgetOptions = [];

    /**
     * @var string default: html
     */
    public $defaultTextFormat;

    /**
     * @param \yii\base\Component $owner
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if (!$this->textFormats && isset(Yii::$app->params['textFormats'])) {
            $this->textFormats = Yii::$app->params['textFormats'];
        }
        if (!$this->textFormats) {
            $this->textFormats = [
                'text' => [
                    'label' => 'Text',
                    'formatter' => function ($text, $language) {
                        return Yii::$app->formatter->asNtext($text);
                    }
                ],
                'html' => [
                    'label' => 'HTML',
                    'default' => true,
                ],
            ];
        }

        if (!$this->defaultTextFormat) {
            foreach ($this->textFormats as $id=>$format) {
                if (!$this->defaultTextFormat) {
                    $this->defaultTextFormat = $id;
                }
                if (isset($format['default']) && $format['default']) {
                    $this->defaultTextFormat = $id;
                    break;
                }
            }
        }
    }

    /**
     * @param string $format
     * @return array|null
     */
    public function getTextFormatInfo($format)
    {
        if (isset($this->textFormats[$format])) {
            return $this->textFormats[$format];
        }
        return null;
    }

    /**
     * @param string $format
     * @param string $text
     * @param string $language
     * @return string
     */
    public function getFormattedText($format, $text, $language=null)
    {
        $text = trim($text);
        $format = $this->getTextFormatInfo($format);
        if ($format && isset($format['formatter']) && $format['formatter'] instanceof \Closure) {
            if (!$language) {
                $language = Yii::$app->language;
            }
            return call_user_func($format['formatter'], $text, $language);
        }
        return $text;
    }
}