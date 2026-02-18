<?php

/** @var yii\web\View $this */
/** @var app\models\forms\ShortenUrlForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;

$this->title = 'Сервис коротких ссылок + QR';
$this->registerJsFile('@web/js/shortener.js', ['depends' => [JqueryAsset::class]]);
?>

<div class="site-index">
    <div class="row justify-content-center mt-4">
        <div class="col-lg-8">
            <h1 class="h2 mb-3">Сервис коротких ссылок + QR</h1>
            <p class="text-muted mb-4">
                Вставьте URL сайта, нажмите <strong>OK</strong> и получите короткую ссылку с QR-кодом.
            </p>

            <?php $form = ActiveForm::begin([
                'id' => 'shorten-form',
                'action' => Url::to(['link/create']),
                'method' => 'post',
                'enableClientValidation' => false,
                'options' => ['class' => 'mb-3'],
            ]); ?>

            <div class="input-group input-group-lg">
                <?= $form->field($model, 'url', [
                    'template' => '{input}{error}',
                    'options' => ['class' => 'flex-grow-1 mb-0'],
                ])->textInput([
                    'placeholder' => 'https://example.com',
                    'autocomplete' => 'off',
                    'id' => 'url-input',
                ]) ?>
                <?= Html::submitButton('OK', ['class' => 'btn btn-primary', 'id' => 'shorten-submit']) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <div id="shortener-message" class="alert d-none" role="alert"></div>

            <div id="shortener-result" class="card d-none">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <img id="qr-code-image" src="" alt="QR Code" class="img-fluid border rounded p-2">
                        </div>
                        <div class="col-md-8">
                            <h2 class="h5">Короткая ссылка</h2>
                            <a id="short-link-url" href="#" target="_blank" rel="noopener noreferrer"></a>
                            <p class="text-muted mt-2 mb-0">
                                Откройте QR в камере телефона, чтобы перейти по ссылке.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
