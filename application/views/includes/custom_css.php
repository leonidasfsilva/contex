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
</style>
