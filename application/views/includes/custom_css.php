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
</style>
