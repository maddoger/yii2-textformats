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
     * @var \yii\base\Object
     */
    public $context;

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
        if (!$this->context) {
            $this->context = Yii::$app->controller;
            if (
                !isset($this->context->textFormats) ||
                !isset($this->context->textEditorWidgetOptions)
            ) {
                $this->context = Yii::$app->module;
            }
        }
        if (
            !isset($this->context->textFormats) ||
            !isset($this->context->textEditorWidgetOptions)
        ) {
            throw new InvalidParamException('Invalid context. Add TextFormatsBehavior to module.');
        }
        
        if ($this->format && !is_array($this->format) && $this->context) {
            if (!isset($this->context->textFormats[$this->format])) {
                throw new InvalidParamException('Format not found.');
            }
            $this->format = $this->context->textFormats[$this->format];
        }
    }

    public function run()
    {
        $value = $this->hasModel() ? $this->model->{$this->attribute} : $this->name;
        $name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;

        if (isset($this->format['widgetClass'])) {
            $widgetClass = $this->format['widgetClass'];
            $options = isset($this->format['widgetOptions']) ? $this->format['widgetOptions'] : [];
            if (isset($this->context->textEditorWidgetOptions) && !empty($this->context->textEditorWidgetOptions)) {
                $options = ArrayHelper::merge($options, $this->context->textEditorWidgetOptions);
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