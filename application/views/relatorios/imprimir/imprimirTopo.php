<div>
    <br>
    <div style="width: 25%; float: left" class="float-left col-md-3">
        <img style="width: 150px" src="<?= base_url() . 'assets/uploads/logomarcas/' . $emitente->logomarca ?>" alt=""><br><br>
    </div>
    <div style="float: right; padding-bottom: 20px">
        <b>EMPRESA: </b> <?= $emitente->emitente ?><br> <b>CNPJ: </b> <?= $emitente->cnpj ?><br>
        <b>ENDEREÇO: </b> <?= $emitente->logradouro ?>, <?= $emitente->numero ?>, <?= $emitente->bairro ?>, <?= $emitente->cidade ?> - <?= $emitente->uf ?> <br>
        <b>RELATÓRIO: </b> <?= $title ?> <br>
        <b>DATA INICIAL: </b> <?= $dataInicial ?> <b>DATA FINAL: </b> <?= $dataFinal ?>
    </div>
</div>