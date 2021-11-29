<?php

use Paytic\Omnipay\Common\Library\View\View;

/**
 * @var View $this
 */

$response = $this->get('response');
//$model = $response->getModel();
$messageType = $response->getMessageType();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?php echo $this->get('title'); ?></title>
    <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Open+Sans:400,300'>
    <link rel="stylesheet" type='text/css' href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type='text/css' href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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
    <h1 style="font-size: 36px; color: <?php echo $response->getIconColor() ?>">
        <i class="<?php echo $response->getIconClass() ?>" aria-hidden="true"></i>
        <?php echo $this->get('title'); ?>
    </h1>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php if ($this->has('subtitle')) {
    ?>
                <h4>
                    <?php echo $this->get('subtitle'); ?>
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
            <div class="alert <?php echo $alertType; ?>">
                <?php echo $this->get('message'); ?>
            </div>


            <?php if ($response->isRedirect() || $response->hasButton()) {
                ?>
                <?php $src = $response->isRedirect() ? $response->getRedirectUrl() : $response->getButtonHref() ?>
                <?php $label = $response->hasButton() ? $response->getButtonLabel() : 'Click here to continue'; ?>

                <form action="<?php echo $src ?>" name="form-confirm" id="form-confirm" method="POST">
                    <?php if ($response->isRedirect()) {
                    ?>
                        <p>
                            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                            Redirecting
                        </p>
                        <?php
                } ?>
                    <button class="btn btn-success btn-md">
                        <i class="fa fa-mouse-pointer" aria-hidden="true"></i>
                        <?php echo $response->getButtonLabel(); ?>
                    </button>
                </form>

            <?php if ($response->isRedirect()) {
                    ?>
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
<?php
echo $this->get('footer_body');
?>
</body>
</html>