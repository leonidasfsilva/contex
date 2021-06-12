<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notificacoes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $data['notificacoes'] = $this->notificacoes_model->getNotificacoesUsuario();
        $data['view'] = 'notificacoes/notificacoes';
        $this->load->view('tema/topo', $data);
    }

    public function adicionar()
    {
        $data = $this->input->post();
        $return = setNotification(
            $idUsuario = $data['id_usuario'],
            $descricao = $data['descricao'],
            $icone = $data['icone'],
            $link = $data['link'],
            $prioridade = $data['prioridade'],
        );
        if ($return) {
            $this->session->set_flashdata('sucesso', 'Notificação registrada com sucesso!');
            redirect(base_url() . 'notificacoes');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar registrar notificação.');
            redirect(base_url() . 'notificacoes');
        }
    }

    public function lerTodasNotificacoes()
    {
        $this->notificacoes_model->lerTodasNotificacoes(getUserId());
    }

    public function lerNotificacao()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $this->notificacoes_model->lerNotificacao($this->input->post('id'));
    }

    // Método para atualizar lista de notificacoes do usuario.
    public function atualizaNotificacoesUsuario()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        } else {
            $user_id = $this->session->userdata('id');

            if ($user_id) {
                $logado = true;
            } else {
                $logado = false;
            }

            $notificacoes = $this->notificacoes_model->atualizaNotificacoesUsuario(getUserId());
            $qnt = $this->notificacoes_model->usuarioTemNotificacoes(getUserId());

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
