<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet"/>

<link href="<?php echo config_item('base_url'); ?>/assets/font-awesome-6/css/all.css" rel="stylesheet">
<link href="<?php echo config_item('base_url'); ?>/assets/css/styles-blessed3.css" type="text/css" rel="stylesheet">                                     <!-- Core CSS with all styles -->
<link href="<?php echo config_item('base_url'); ?>/assets/css/styles-blessed2.css" type="text/css" rel="stylesheet">                                     <!-- Core CSS with all styles -->
<link href="<?php echo config_item('base_url'); ?>/assets/css/styles-blessed1.css" type="text/css" rel="stylesheet">                                     <!-- Core CSS with all styles -->
<link href="<?php echo config_item('base_url'); ?>/assets/css/styles.css" type="text/css" rel="stylesheet">                                     <!-- Core CSS with all styles -->
<link href="<?php echo config_item('base_url'); ?>/assets/css/custom.css" type="text/css" rel="stylesheet">                                     <!-- Core CSS with all styles -->

<head>
    <meta charset="utf-8">
    <title>404 Page Not Found</title>
    <style>

        ::selection {
            /*background-color: #E13300;*/
            /*color: white;*/
        }

        ::-moz-selection {
            background-color: #E13300;
            color: white;
        }

        body {
            font-optical-sizing: auto;
            font-weight: normal;
            font-style: normal;

            font-family: Inter, sans-serif;
            background-color: #fff;
            margin: 40px;
            /*font: 13px/20px normal;*/
            color: #4F5155;
        }

        a {
            /*color: #003399;*/
            /*background-color: transparent;*/
            /*font-weight: normal;*/
        }

        h1 {
            color: #444;
            background-color: transparent;
            border-bottom: 1px solid #D0D0D0;
            font-size: 19px;
            font-weight: normal;
            margin: 0 0 14px 0;
            padding: 14px 15px 10px 15px;
        }

        code {
            font-family: Consolas, Monaco, Courier New, Courier, monospace;
            font-size: 12px;
            background-color: #f9f9f9;
            border: 1px solid #D0D0D0;
            color: #002166;
            display: block;
            margin: 14px 0 14px 0;
            padding: 12px 10px 12px 10px;
        }

        #container {
            margin: 10px;
            border: 1px solid #D0D0D0;
            box-shadow: 0 0 8px #D0D0D0;
        }

        p {
            /*margin: 12px 15px 12px 15px;*/
        }
    </style>
</head>
<body>
<div class="row">
    <div class="panel panel-midnightblue">
        <div class="panel-heading">
            <h3>
                <i class="fal fa-hexagon-exclamation fa-lg"></i>
                404 Não Encontrado
            </h3>
        </div>
        <div class="panel-body">
            <div class="note note-danger font-weight-bold mb30 ">
                <h3 class="text-alizarin">Página não encontrada.</h3>
                Desculpe, mas a página solicitada não existe.
            </div>
            <button class="btn btn-default-alt" style="text-decoration: none" onclick="javascript:history.back()">
                <i class="fas fa-arrow-left fa-fw"></i>
                Voltar
            </button>
        </div>
    </div>
</div>
</body>
</html>