<?php
/**
 * @copyright Copyright (c) 2014 Vitaliy Syrchikov
 * @link http://syrchikov.name
 */

namespace maddoger\textformats\actions;

use Yii;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\web\NotAcceptableHttpException;

/**
 * ChangeFormatAction
 *
 * @author Vitaliy Syrchikov <maddoger@gmail.com>
 * @link http://syrchikov.name
 * @package maddoger/yii2-textformats
 */
class ChangeFormatAction extends Action
{
    /**
     * @var \yii\base\Object
     */
    public $context;
    
    /**
     * @var string
     */
    public $view;

    /**
     * @var string
     */
    public $textFormatField = 'text_format';

    /**
     * @var string
     */
    public $textField = 'text_source';

    /**
     * @return string
     * @throws NotAcceptableHttpException
     */
    public function run()
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotAcceptableHttpException('This action AJAX only!');
        }

        $post = Yii::$app->request->post();
        $keys = array_keys($post);
        $formName = $keys[0];
        if (!isset($post[$formName][$this->textFormatField])) {
            throw new InvalidParamException('Invalid POST data.');
        }
        $format = $post[$formName][$this->textFormatField];
        $text = $post[$formName][$this->textField];

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

        $formats = $this->context->textFormats;

        if (!isset($formats[$format])) {
            throw new InvalidParamException('Format not found.');
        }

        $params = [
            'fieldName' => $formName . '['.$this->textField .']',
            'text' => $text,
            'formatInfo' => $formats[$format],
            'widgetOptions' => $this->context->textEditorWidgetOptions,
        ];

        if ($this->view) {
            return $this->controller->renderAjax($this->view, $params);
        } else {
            return $this->controller->renderAjax('@vendor/maddoger/yii2-textformats/views/changeFormat.php', $params);
        }
    }
}