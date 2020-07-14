<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<link href="././assets/font-awesome5/css/all.css" rel="stylesheet">
<link href="././assets/css/styles-blessed3.css" type="text/css" rel="stylesheet">                                     <!-- Core CSS with all styles -->
<link href="././assets/css/styles-blessed2.css" type="text/css" rel="stylesheet">                                     <!-- Core CSS with all styles -->
<link href="././assets/css/styles-blessed1.css" type="text/css" rel="stylesheet">                                     <!-- Core CSS with all styles -->
<link href="././assets/css/styles.css" type="text/css" rel="stylesheet">                                     <!-- Core CSS with all styles -->

<head>
    <meta charset="utf-8">
    <title>404 Page Not Found</title>
    <style type="text/css">

        ::selection {
            /*background-color: #E13300;*/
            /*color: white;*/
        }

        ::-moz-selection {
            background-color: #E13300;
            color: white;
        }

        body {
            background-color: #fff;
            margin: 40px;
            font: 13px/20px normal Helvetica, Arial, sans-serif;
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
            <h2><?php echo $heading; ?></h2>
        </div>
        <div class="panel-body">
            <div class="alert alert-danger font-weight-bold">A página requistada não foi encontrada em nosso servidor, clique no botão abaixo para retornar à página inicial.</div>
            <p>
                <a class="btn btn-primary" style="text-decoration: none" href="<?php echo config_item('base_url'); ?>"><i class="fas fa-arrow-left fa-fw"></i>
                    Clique aqui para voltar
                </a>
            </p>
        </div>
    </div>
</div>
</body>
</html>