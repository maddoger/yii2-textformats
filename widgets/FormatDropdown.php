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
use yii\helpers\Url;
use yii\widgets\InputWidget;

/**
 * FormatDropdown
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package maddoger/yii2-textformats
 */
class FormatDropdown extends InputWidget
{
    /**
     * @var \yii\base\Object
     */
    public $context;

    /**
     * @var array
     */
    public $options = ['class' => 'form-control'];

    /**
     * @var string
     */
    public $changeFormatMessage;

    /**
     * @var array
     */
    public $changeFormatUrl;

    /**
     * @var string text format attribute
     */
    public $textAttribute = 'text_source';

    /**
     * @var string jquery text field selector
     */
    public $textEditorSelector;

    /**
     * @var string jquery text field selector
     */
    public $textEditorContainerSelector = '.text-editor';

    /**
     * @var bool
     */
    public $registerClientScripts = true;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->changeFormatMessage) {
            $this->changeFormatMessage = Yii::t('app', 'Are you sure want to change text format and load another editor?');
        }
        if (!$this->changeFormatUrl) {
            $this->changeFormatUrl = ['change-format'];
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
    }

    public function run()
    {
        if ($this->registerClientScripts) {
            $this->registerClientScripts();
        }
        $items = ArrayHelper::getColumn($this->context->textFormats, 'label', true);
        if ($this->hasModel()) {
            return Html::activeDropDownList($this->model, $this->attribute, $items, $this->options);
        } else {
            return Html::dropDownList($this->name, $this->value, $items, $this->options);
        }
    }

    public function registerClientScripts()
    {
        $url = Url::to($this->changeFormatUrl);
        if (!$this->textEditorSelector && $this->hasModel() && $this->textAttribute) {
            $this->textEditorSelector = '[name="'.Html::getInputName($this->model, $this->textAttribute).'"]';
        }
        $this->view->registerJs(
<<<JS
    //Change editor
    $('#{$this->options['id']}').change(function(){
        var t = $(this);
        var textarea = $('{$this->textEditorSelector}');
        var textContainer = textarea.closest('{$this->textEditorContainerSelector}');
        if (textarea.val()!='') {
            if (!confirm('{$this->changeFormatMessage}')) {
                $(this).val($(this).data('val'));
                return;
            }
        }
        var data = [
            { name: t.prop('name'), value: t.val() },
            { name: textarea.prop('name'), value: textarea.val() }
        ];
        textContainer.load("{$url}", data, function(response){

        });
    }).on('focus', function(){
        $(this).data('val', $(this).val());
    });
JS
        );
    }
}