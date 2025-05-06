<?php

declare(strict_types=1);

use Paytic\Omnipay\Common\Library\View\View;
use Paytic\Omnipay\Common\Message\Traits\HtmlResponses\ConfirmHtmlTrait;

/**
 * @var View $this
 */

/** @var ConfirmHtmlTrait $response */
$response = $this->get('response');
//$model = $response->getModel();
$messageType = $response->getMessageType();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?= $this->get('title'); ?></title>
    <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Open+Sans:400,300'>
    <link rel="stylesheet" type='text/css' href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <style>
        body {
            font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            padding: 0;
            background-color: #fffffe;
            color: #1a1a1a;
            text-align: center;
        }

        .header {
            margin-top: 100px;
            padding-top: 10px;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1 style="font-size: 36px; color: <?= $response->getIconColor() ?>">
        <i class="<?= $response->getIconClass() ?>" aria-hidden="true"></i>
        <?= $this->get('title'); ?>
    </h1>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col col-md-8 col-lg-7 col-xl-6 align-self-center">
            <?php
            if ($this->has('subtitle')) { ?>
                <h4>
                    <?= $this->get('subtitle'); ?>
                </h4>
                <hr/>
                <?php
            } ?>
            <p>&nbsp;</p>

            <?php
            $alertType = 'alert-info';
            switch ($messageType) {
                case 'error':
                    $alertType = 'alert-warning';
                    break;
                case 'success':
                case 'info':
                    $alertType = 'alert-' . $messageType;
                    break;
            }
            ?>
            <div class="alert <?= $alertType; ?>">
                <p class="fs-3">
                    <?= $this->get('message'); ?>
                </p>
                <p class="font-monospace fs-5 text-white bg-dark">
                    <?= $response->getMessageDescription(); ?>
                </p>
            </div>

            <?php
            if ($response->isRedirect() || $response->hasButton()) {
                ?>
                <?php
                $src = $response->isRedirect() ? $response->getRedirectUrl() : $response->getButtonHref();
                $label = $response->hasButton() ? $response->getButtonLabel() : 'Click here to continue';
                ?>

                <form action="<?= $src ?>" name="form-confirm" id="form-confirm" method="POST">
                    <?php
                    if ($response->isRedirect()) {
                        ?>
                        <p>
                            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                            Redirecting
                        </p>
                        <?php
                    } ?>
                    <button class="btn btn-success btn-md">
                        <i class="fa fa-mouse-pointer" aria-hidden="true"></i>
                        <?= $response->getButtonLabel(); ?>
                    </button>
                </form>

            <?php
            if ($response->isRedirect()) { ?>
                <script>
                    var timer = setTimeout(function () {
                        document.forms[0].submit();
                    }, 3000);
                </script>
                <?php
            } ?>
                <?php
            } ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
<?= $this->get('footer_body'); ?>
</body>
</html>