<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?> - <?php echo UNIV_NAME; ?></title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="images/favicon-uca.png" />
</head>

<body class="">
    <?php if (APP_MODE_TEST == "YES") { ?>
        <div class="is-test">
            Site Test
        </div>
    <?php } ?>
    <?php if (APP_MODE_DEBUG == "YES") { ?>
        <div class="is-debug">
            Mode Debug
        </div>
    <?php } ?>