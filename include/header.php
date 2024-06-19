<?php
/**
 * ApoSE header.php
 * php version 7
 *
 * @category Education
 * @package  Apose
 * @author   2014 - CRI Université Lille 2 <cri@univ-lille.fr>
 * @author   2021-2024 - UniCA DSI SEN <dsi.sen@univ-cotedazur.fr>
 * @author   2022 - Université Toulouse 1 Capitole <dsi@univ-tlse1.fr>
 * @license  GNU GPL
 * @link     https://git.unice.fr/dsi-sen/apose
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?> - <?php echo UNIV_NAME; ?></title>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css"
    >
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/tofsjonas/sortable@latest/sortable.min.css"
    >
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>

<body class="">
    <?php if (APP_MODE_TEST === "YES") { ?>
        <div class="is-test">
            Site Test
        </div>
    <?php } ?>
    <?php if (APP_MODE_DEBUG === "YES") { ?>
        <div class="is-debug">
            Mode Debug
        </div>
    <?php } ?>