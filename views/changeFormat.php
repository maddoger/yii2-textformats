<?php

/* @var yii\web\View $this */
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var string $text */
/* @var string $fieldName */
/* @var array $formatInfo */
/* @var array $widgetOptions */

if (isset($formatInfo['widgetClass'])) {
    $widgetClass = $formatInfo['widgetClass'];
    $options = isset($formatInfo['widgetOptions']) ? $formatInfo['widgetOptions'] : [];
    if (isset($widgetOptions) &&is_array($widgetOptions) && !empty($widgetOptions)) {
        $options = ArrayHelper::merge($options, $widgetOptions);
    }
    $options['name'] = $fieldName;
    $options['value'] = $text;
    echo $widgetClass::widget($options);
} else {
    echo Html::textarea($fieldName, $text, ['class' => 'form-control', 'rows' => 20]);
}