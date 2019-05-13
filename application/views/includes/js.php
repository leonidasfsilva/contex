<!--    JS CORE-->

<script src="<?php echo base_url(); ?>assets/js/jquery-1.10.2.min.js"></script>                            <!-- Load jQuery -->
<script src="<?php echo base_url(); ?>assets/js/jqueryui-1.9.2.min.js"></script>                            <!-- Load jQueryUI -->
<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>                                <!-- Load Bootstrap -->

<!--<script src="--><?php //echo base_url(); ?><!--assets/plugins/pines-notify/pnotify.min.js"></script>-->
<script src="<?php echo base_url(); ?>assets/plugins/jquery-notific8/jquery.notific8.js"></script>         <!-- Load Notific8 -->

<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-switch/bootstrap-switch.js"></script>         <!-- Load Bootstrap Switch JS -->

<script src="<?php echo base_url(); ?>assets/plugins/easypiechart/jquery.easypiechart.js"></script>        <!-- EasyPieChart-->
<script src="<?php echo base_url(); ?>assets/plugins/sparklines/jquery.sparklines.min.js"></script>        <!-- Sparkline -->
<script src="<?php echo base_url(); ?>assets/plugins/jstree/dist/jstree.min.js"></script>                <!-- jsTree -->

<script src="<?php echo base_url(); ?>assets/plugins/codeprettifier/prettify.js"></script>                <!-- Code Prettifier  -->

<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-tabdrop/js/bootstrap-tabdrop.js"></script>  <!-- Bootstrap Tabdrop -->

<script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js"></script>

<script src="<?php echo base_url(); ?>assets/js/enquire.min.js"></script>                                    <!-- Enquire for Responsiveness -->

<script src="<?php echo base_url(); ?>assets/plugins/bootbox/bootbox.js"></script>                            <!-- Bootbox -->

<script src="<?php echo base_url(); ?>assets/plugins/simpleWeather/jquery.simpleWeather.min.js"></script> <!-- Weather plugin-->

<script src="<?php echo base_url(); ?>assets/plugins/nanoScroller/js/jquery.nanoscroller.min.js"></script> <!-- nano scroller -->

<script src="<?php echo base_url(); ?>assets/plugins/jquery-mousewheel/jquery.mousewheel.min.js"></script>    <!-- Mousewheel support needed for jScrollPane -->
<script src="<?php echo base_url(); ?>assets/plugins/sweetalert2/sweetalert2.js"></script>

<script src="<?php echo base_url(); ?>assets/js/application.js"></script>
<script src="<?php echo base_url(); ?>assets/demo/demo.js"></script>
<script src="<?php echo base_url(); ?>assets/demo/demo-switcher.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/wijets/wijets.js"></script>
<!--    <script src="--><?php //echo base_url(); ?><!--assets/plugins/form-daterangepicker/moment.min.js"></script>                -->
<!--    <script src="--><?php //echo base_url(); ?><!--assets/plugins/form-daterangepicker/daterangepicker.js"></script>           -->
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.pt-BR.min.js"></script>

<!-- Pnotify-->

<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotify.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotifyAnimate.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotifyButtons.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotifyCallbacks.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotifyCompat.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotifyConfirm.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotifyDesktop.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotifyHistory.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotifyMobile.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotifyNonBlock.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotifyReference.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/pnotify/iife/PNotifyStyleMaterial.js"></script>


<script>
    <?php
    $url = current_url();
    $segments = explode("/", $url);
    $bloqueados = array(
        'login',
        'redefinirsenha',
    );

    if(!count(array_intersect($segments, $bloqueados)) > 0){ ?>
    window.onload = function () {
        setTimeout(function () {
            var wrapper = document.body;
            wrapper.className += " page-loading";
        }, 500);
    };
    <?php
    } ?>

    $(document).ready(function () {
        setTimeout(function () {
            $('body').removeClass('page-loading');
        }, 2600);

    });

    $('#btn_teste').click(function () {
        Swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            position: 'top',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                )
            }
        });
    });

    $('.datepicker').datepicker({
        language: 'pt-BR',
        autoclose: true,
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        todayBtn: true
    });

    $('.tooltips').tooltip();

    $('.poupanca').click(function () {
        Swal.fire({
            position: 'top',
            type: 'info',
            // timer: 5000,
            title: 'Em breve',
            html: 'O módulo de Poupança encontra-se em desenvolvimento.',
            showConfirmButton: false,
            showCancelButton: true,
            showCloseButton: true,
            reverseButtons: true,
            confirmButtonText: '<i class="fa fa-refresh fa-fw"></i> Tentar de novo ',
            cancelButtonText: '<i class="fa fa-times fa-fw"></i> Fechar ',
        }).then((result) => {
            if (result.value) {
                recuperar_senha();
            } else {

            }
        });

        // PNotify.info({
        //     title: 'Em breve',
        //     text: 'O módulo de Poupança encontra-se em desenvolvimento.',
        //     styling: 'bootstrap3',
        //     icon: 'fa fa-warning fa-lg fa-fw',
        //     delay: 5000,
        //     addClass: 'pnotify-shadow',
        //     hide: true,
        //     stack: {
        //         'dir1': 'down',
        //         'firstpos1': 25
        //     },
        //     modules: {
        //         Animate: {
        //             animate: true,
        //             inClass: 'slideInDown',
        //             outClass: 'slideOutUp'
        //         },
        //         Buttons: {
        //             sticker: false,
        //             closerHover: false,
        //         },
        //         Mobile: {
        //             styling: true
        //         }
        //     }
        // });
    });

    $(document).ready(function () {
        <?php if ($this->session->flashdata('erro') != null) { ?>
        Swal.fire({
            position: 'top',
            type: 'error',
            timer: 3000,
            title: 'Erro!',
            html: '<?= $this->session->flashdata('erro') ?>',
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true,
            reverseButtons: true,
            confirmButtonText: '<i class="fa fa-refresh fa-fw"></i> Tentar de novo ',
            cancelButtonText: '<i class="fa fa-times fa-fw"></i> Fechar ',
        }).then((result) => {
            if (result.value) {
                recuperar_senha();
            } else {

            }
        });

        //PNotify.error({
        //    title: 'Erro!',
        //    text: '<?//= $this->session->flashdata('erro') ?>//',
        //    styling: 'bootstrap3',
        //    icon: 'fa fa-times-circle fa-lg fa-fw',
        //    delay: 3000,
        //    addClass: 'pnotify-center',
        //    hide: true,
        //    stack: {
        //        'dir1': 'down',
        //        'firstpos1': 25
        //    },
        //    modules: {
        //        Animate: {
        //            animate: true,
        //            inClass: 'slideInDown',
        //            outClass: 'slideOutUp'
        //        },
        //        Buttons: {
        //            sticker: false,
        //            closerHover: false,
        //        },
        //        Mobile: {
        //            styling: true
        //        }
        //    }
        //});
        <?php } ?>

        <?php if ($this->session->flashdata('sucesso') != null) { ?>
        Swal.fire({
            position: 'top',
            type: 'success',
            title: 'Feito!',
            timer: 3000,
            html: '<?= $this->session->flashdata('sucesso') ?>',
            showConfirmButton: false,
            showCancelButton: false,
            showCloseButton: true,
            confirmButtonText: '<i class="fa fa-check fa-fw"></i> OK ',
            cancelButtonText: '<i class="fa fa-times fa-fw"></i> Fechar ',
            reverseButtons: true,
        }).then((result) => {
            if (result.value) {

            } else {

            }
        });

        //PNotify.success({
        //    title: 'Feito!',
        //    text: '<?//= $this->session->flashdata('sucesso') ?>//',
        //    styling: 'bootstrap3',
        //    icon: 'fa fa-check-circle fa-lg fa-fw',
        //    delay: 3000,
        //    addClass: 'pnotify-center',
        //    hide: true,
        //    stack: {
        //        'dir1': 'down',
        //        'firstpos1': 25
        //    },
        //    modules: {
        //        Animate: {
        //            animate: true,
        //            inClass: 'slideInDown',
        //            outClass: 'slideOutUp'
        //        },
        //        Buttons: {
        //            sticker: false,
        //            closerHover: false,
        //        },
        //        Mobile: {
        //            styling: true
        //        }
        //    }
        //});
        <?php } ?>
    });

</script>


</head>
