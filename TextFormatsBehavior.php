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
     * `formatter` - source to html Closure `string function($model)`. If is null, copy will be used;
     * `widgetClass` - widget class for editor
     * `widgetOptions` - widget options
     */
    public $textFormats;

    /**
     * @var array additional options for widget
     */
    public $textEditorWidgetOptions = [];

    /**
     * @var array
     */
    public $changeFormatUrl = ['textformats/change-format/index'];

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
                    'formatter' => function ($text) {
                        return Yii::$app->formatter->asNtext($text);
                    }
                ],
                'html' => [
                    'label' => 'HTML',
                ],
            ];
        }
    }
}