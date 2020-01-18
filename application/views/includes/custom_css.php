<style>
    .switch-input {
        display: none;
    }
    .switch-label {
        position: relative;
        display: inline-block;
        min-width: 112px;
        cursor: pointer;
        font-weight: 500;
        text-align: left;
        margin: 16px;
        padding: 16px 0 16px 44px;
    }
    .switch-label:before, .switch-label:after {
        content: "";
        position: absolute;
        margin: 0;
        outline: 0;
        top: 50%;
        -ms-transform: translate(0, -50%);
        -webkit-transform: translate(0, -50%);
        transform: translate(0, -50%);
        -webkit-transition: all 0.3s ease;
        transition: all 0.3s ease;
    }
    .switch-label:before {
        left: 1px;
        width: 34px;
        height: 14px;
        background-color: #c6c6c6;
        border-radius: 8px;
    }
    .switch-label:after {
        left: 0;
        width: 20px;
        height: 20px;
        background-color: #FAFAFA;
        border-radius: 50%;
        box-shadow: 0px 0px 3px 1px rgba(0, 0, 0, 0.14), 0 2px 5px 0 rgba(0, 0, 0, 0.098), 0 1px 5px 0 rgba(0, 0, 0, 0.084);
    }
    .switch-label .toggle--on {
        display: none;
    }
    .switch-label .toggle--off {
        display: inline-block;
    }
    .switch-input:checked + .switch-label:before {
        background-color: #a6d3cf;
    }
    .switch-input:checked + .switch-label:after {
        background-color: #009385;
        -ms-transform: translate(80%, -50%);
        -webkit-transform: translate(80%, -50%);
        transform: translate(80%, -50%);
    }
    .switch-input:checked + .switch-label .toggle--on {
        display: inline-block;
    }
    .switch-input:checked + .switch-label .toggle--off {
        display: none;
    }



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
        position: fixed;
        z-index: 1009;
        height: 2px;
        width: 100%;
        margin-top: 48px;
        left: 0;
        background-color: #dd0006;
        animation: page-load ease-out 2s;
        box-shadow: 0 2px 2px rgba(0, 0, 0, .2);
    }

    label.error, .error {
        color: #b94a48;
    }

    input.error {
        border-color: #b94a48;
    }

    input.valid {
        border-color: #5bb75b;
    }

    .table-bordeless td, .table-bordeless th {
        border: none;
    }

    table {
        font-family: Arial;
        font-size: 11px;
    }

    .preloader {
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        position: absolute;
        z-index: 1;
        background: #eee;
    }

    .preloader .cssload-speeding-wheel {
        position: absolute;
        top: calc(25% - 3%);
        left: calc(50% - 3%);
    }

    /*@media screen and (max-width: 768px) {*/
    /*    #menu-toggle-icon {*/
    /*        display: none;*/
    /*    }*/
    /*    .menu-toggle:before {*/
    /*        font-family: FontAwesome;*/
    /*        content: "\f142";*/
    /*    }*/
    /*}*/
    /*body.infobar-offcanvas.sidebar-collapsed .menu-toggle:before {*/
    /*    font-family: FontAwesome;*/
    /*    content: "\f0c9";*/

    /*}*/
    /*body.infobar-offcanvas .menu-toggle:before {*/
    /*    font-family: FontAwesome;*/
    /*    content: "\f00d";*/
    /*}*/

    .user-avatar {
        width: 128px;
        height: 128px;
    }

    .cropper-container {
        max-height: 400px !important;
    }

    .image-cropped {
        width: auto;
        height: auto;
    }

    .avatar-cropped {
        width: 128px;
        height: 128px;
        margin-left: -64px;
        left: 50%;
        position: relative;
        margin-bottom: 16px;
    }

</style>
