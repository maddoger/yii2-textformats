<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\textformats\widgets;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * TextEditor
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package maddoger/yii2-textformats
 */
class TextEditor extends InputWidget
{
    /**
     * @var string text format attribute
     */
    public $formatAttribute = 'text_format';

    /**
     * @var string|array
     */
    public $format;

    /**
     * @var \yii\base\Module
     */
    public $module;

    /**
     * @var array
     */
    public $widgetOptions;

    /**
     * @var array
     */
    public $options = ['class' => 'form-control', 'rows' => 20];

    /**
     * @var array
     */
    public $containerOptions = ['tag' => 'div', 'class' => 'text-editor'];

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!$this->format && $this->hasModel() && $this->formatAttribute) {
            $this->format = $this->model->{$this->formatAttribute};
        }
        if (!$this->module) {
            $this->module = Yii::$app->controller->module;
        }
        if (
            !isset($this->module->textFormats) ||
            !isset($this->module->textEditorWidgetOptions)
        ) {
            throw new InvalidParamException('Invalid module. Add TextFormatsBehavior to module.');
        }
        if ($this->format && !is_array($this->format) && $this->module) {
            if (!isset($this->module->textFormats[$this->format])) {
                throw new InvalidParamException('Format not found.');
            }
            $this->format = $this->module->textFormats[$this->format];
        }
    }

    public function run()
    {
        $value = $this->hasModel() ? $this->model->{$this->attribute} : $this->name;
        $name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;

        if (isset($this->format['widgetClass'])) {
            $widgetClass = $this->format['widgetClass'];
            $options = isset($this->format['widgetOptions']) ? $this->format['widgetOptions'] : [];
            if (isset($this->module->textEditorWidgetOptions) && !empty($this->module->textEditorWidgetOptions)) {
                $options = ArrayHelper::merge($options, $this->module->textEditorWidgetOptions);
            }
            if (!empty($this->widgetOptions)) {
                $options = ArrayHelper::merge($options, $this->widgetOptions);
            }
            $options['name'] = $name;
            $options['value'] = $value;
            $content = $widgetClass::widget($options);
        } else {
            $content = Html::textarea($name, $value, $this->options);
        }

        $containerOptions = $this->containerOptions;
        $tag = ArrayHelper::remove($containerOptions, 'tag', 'div');
        return Html::tag($tag, $content, $containerOptions);
    }
}