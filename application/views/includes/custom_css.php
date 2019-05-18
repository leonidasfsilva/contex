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
        top: 0px;
        left: 0;
        position: absolute;
        z-index: 1;
        background: #eee;
    }
    .preloader .cssload-speeding-wheel {
        position: absolute;
        top: calc(50% - 3.5px);
        left: calc(50% - 3.5px);
    }
</style>
