<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>CONTEX - Sistema de Gestão</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="description" content="Contex Sistema de Gestão">
    <meta name="author" content="Leônidas Ferreira">
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/logo_contex.png" type="image/x-icon"/>


    <!--    CSS CORE-->
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,400italic,600,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,400italic,600,700' rel='stylesheet' type='text/css'>
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/fullcalendar.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/plugins/sweetalert2/sweetalert2.css" type="text/css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.toast.min.css">

    <!--    <link href="--><?php //echo base_url(); ?><!--assets/fonts/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet">       -->
    <link href="<?php echo base_url(); ?>assets/css/styles.css" type="text/css" rel="stylesheet">                                     <!-- Core CSS with all styles -->
    <!--<link href="--><?php //echo base_url(); ?><!--assets/css/sweetalert2.css" type="text/css" rel="stylesheet"> -->
    <link href="<?php echo base_url(); ?>assets/plugins/jstree/dist/themes/avenger/style.min.css" type="text/css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/plugins/codeprettifier/prettify.css" type="text/css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/plugins/iCheck/skins/minimal/blue.css" type="text/css" rel="stylesheet">

    <link href="<?php echo base_url(); ?>assets/css/animate.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/plugins/jquery-notific8/jquery.notific8.css" type="text/css" rel="stylesheet">              <!-- Load Notific8 CSS -->
    <!--<link href="--><?php //echo base_url(); ?><!--assets/plugins/pines-notify/pnotify.css" type="text/css" rel="stylesheet">-->

    <!-- The following CSS are included as plugins and can be removed if unused-->
    <link href="<?php echo base_url(); ?>assets/plugins/form-daterangepicker/daterangepicker-bs3.css" type="text/css" rel="stylesheet">    <!-- DateRangePicker -->
    <link href="<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.css" type="text/css" rel="stylesheet">                    <!-- FullCalendar -->
    <link href="<?php echo base_url(); ?>assets/plugins/bootstrap-switch/bootstrap-switch.css" type="text/css" rel="stylesheet"> <!--Bootstrap Switch CSS-->
    <link href="<?php echo base_url(); ?>assets/plugins/bootstrap-datepicker/bootstrap-datepicker.css" type="text/css" rel="stylesheet"> <!--Bootstrap Switch CSS-->

    <!--Pnotify CSS-->
    <link href="<?php echo base_url(); ?>assets/plugins/pnotify/PNotifyBrightTheme.css" type="text/css" rel="stylesheet">

    <style>

        @keyframes page-load {
            from {
                width: 0;
            }
            to {
                width: 100%;
            }
        }

        .page-loading::before {
            content: " ";
            display: flex;
            position: absolute;
            z-index: 10;
            height: 10px;
            width: 100%;
            padding-top: 50px;
            left: 0;
            background-color: #00b2ff;
            animation: page-load ease-out 2s;
            box-shadow: 0 2px 2px rgba(0, 0, 0, .2);
        }

    </style>
