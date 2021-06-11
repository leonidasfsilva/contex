<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacoes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function lerTodasNotificacoes()
    {
        $this->notificacoes_model->lerTodasNotificacoes(id_usuario());
    }

    public function lerNotificacao()
    {
        $this->notificacoes_model->lerNotificacao($_POST['id']);
    }

    //Funcao para atualizar lista de notificacoes do usuario.
    public function atualizaNotificacoesUsuario()
    {
        $this->load->model('notificacoes_model', '', true);

        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        } else {
            $user_id = $this->session->userdata('id');

            if ($user_id) {
                $logado = true;
            } else {
                $logado = false;
            }

            $notificacoes = $this->notificacoes_model->atualizaNotificacoesUsuario(id_usuario());
            $qnt = $this->notificacoes_model->usuarioTemNotificacoes(id_usuario());

            if ($notificacoes) {
                $data = array(
                    'result' => true,
                    'retorno' => $notificacoes,
                    'qnt' => $qnt,
                    'logado' => $logado
                );
                echo json_encode($data);
            } else {
                $data = array(
                    'result' => true,
                    'retorno' => null,
                    'qnt' => $qnt,
                    'logado' => $logado,
                    'userdata' => $this->session->userdata()
                );
                echo json_encode($data);
            }
        }
    }

}
