<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Consumo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vConsumo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar consumo de energia.');
            redirect(base_url());
        }

        $this->load->helper('file');
        $this->load->library('upload');
        $this->load->library('image_lib');
    }

    public function index()
    {
        $data['menuChamados'] = true;
        if ($this->session->userdata('permissao') == 1) {
            $data['chamados'] = $this->chamados_model->getChamados();
        } else {
            $data['chamados'] = $this->chamados_model->getChamadosUsuario(id_usuario());
        }
        $data['assuntos'] = $this->chamados_model->getAssuntos();
        $data['view'] = 'consumo/consumo';
        $this->load->view('tema/topo', $data);
    }

    public function pesquisar()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $termo = $this->input->get('termo');

        $data['results'] = $this->mxcode_model->pesquisar($termo, id_usuario());
        $this->data['produtos'] = $data['results']['produtos'];
        $this->data['servicos'] = $data['results']['servicos'];
        $this->data['os'] = $data['results']['os'];
        $this->data['clientes'] = $data['results']['clientes'];
        $this->data['view'] = 'mxcode/pesquisa';
        $this->load->view('tema/topo', $this->data);

    }

    public function abrirChamado()
    {
        $assunto = $_POST['assunto'];
        $descricao = $_POST['descricao'];

        if ($assunto || $descricao == null) {
            $this->session->set_flashdata('erro', 'Preencha os dados do chamado corretamente.');
        }

        $data = array(
            'assunto' => $assunto,
            'descricao' => $descricao,
            'id_usuario' => id_usuario(),
            'status_chamado' => 1,
            'notifica_admin' => 1,
        );

        if ($this->chamados_model->add('chamados', $data) == true) {
            $this->session->set_flashdata('sucesso', 'Chamado aberto com sucesso!');
            redirect('chamados');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar abrir chamado.');
            redirect('chamados');
        }
    }

    public function detalhes($id_chamado = null)
    {
        if ($id_chamado == null) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('chamados');
        }

        $chamado = $this->chamados_model->getDetalhesChamado($id_chamado);
        $data['respostas'] = $this->chamados_model->getRespostasChamado($id_chamado);

        if (!$chamado) {
            $this->session->set_flashdata('erro', 'Chamado não encontrado.');
            redirect('chamados');
        }

        if ($this->session->userdata('permissao') == 1) {
            $data2 = array(
                'notifica_admin' => 0
            );
            $this->chamados_model->edit('chamados', $data2, 'id_chamado', $id_chamado);

            $date_time = new DateTime($chamado->data_abertura);
            $hoje = new DateTime('now');
            $interval = $hoje->diff($date_time);
            $dataformatada = $date_time->format('d/m/Y H:i:s');

            if ($interval->m < 1) {
                if ($interval->d < 1) {
                    if ($interval->h < 1) {
                        $data['intervalo'] = $interval->i . 'm';
                    } else {
                        $data['intervalo'] = $interval->h . 'h';
                    }
                } else {
                    $data['intervalo'] = $interval->d . 'd';
                }
            } else {
                $data['intervalo'] = $dataformatada;
            }

        } else {
            $chamadoUsuario = $this->chamados_model->verificaChamadoPertenceUsuario(id_usuario());
            if ($chamadoUsuario) {
                $data2 = array(
                    'notifica_usuario' => 0
                );
                $this->chamados_model->edit('chamados', $data2, 'id_chamado', $id_chamado);

                $date_time = new DateTime($chamado->data_abertura);
                $dataformatada = $date_time->format('d/m/Y H:i');
                $hoje = new DateTime();
                $interval = $hoje->diff($date_time);

                if ($interval->m < 1) {
                    if ($interval->d < 2) {
                        if ($interval->h < 1) {
                            $data['intervalo'] = $interval->i . 'm';
                        } else {
                            $data['intervalo'] = $interval->h . 'h';
                        }
                    } else {
                        $data['intervalo'] = $interval->d . 'd';
                    }
                } else {
                    $data['intervalo'] = $dataformatada;
                }
            } else {
                $this->session->set_flashdata('erro', 'Chamado não encontrado para este usuário.');
                redirect('chamados');
            }
        }
        $data['chamado'] = $chamado;
        $data['view'] = 'chamados/detalhes';
        $this->load->view('tema/topo', $data);

    }

    public function responder()
    {
        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('chamados');
        }

        if ($this->session->userdata('permissao') == 1) {
            $data = array(
                'resposta' => $_POST['resposta'],
                'id_usuario' => id_usuario(),
                'id_chamado' => $_POST['id_chamado'],
            );
            $data2 = array(
                'notifica_usuario' => 1,
            );
            $this->chamados_model->edit('chamados', $data2, 'id_chamado', $_POST['id_chamado']);
        } else {
            $data = array(
                'resposta' => $_POST['resposta'],
                'id_usuario' => id_usuario(),
                'id_chamado' => $_POST['id_chamado'],
            );
            $data2 = array(
                'notifica_admin' => 1
            );
            $this->chamados_model->edit('chamados', $data2, 'id_chamado', $_POST['id_chamado']);
        }

        if ($this->chamados_model->add('chamados_respostas', $data) == true) {
            $this->session->set_flashdata('sucesso', 'Resposta enviada com sucesso!');
            redirect('chamados/detalhes/' . $_POST['id_chamado']);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar responder chamado.');
            redirect('chamados/detalhes/' . $_POST['id_chamado']);
        }
    }

    public function finalizar()
    {
        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('chamados');
        }

        $data = array(
            'status_chamado' => 3,
            'id_chamado' => $_POST['id_chamado'],
        );

        if ($this->chamados_model->edit('chamados', $data, 'id_chamado', $_POST['id_chamado']) == true) {
            $this->session->set_flashdata('sucesso', 'Chamado finalizado com sucesso!');
            redirect('chamados/detalhes/' . $_POST['id_chamado']);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar finalizar chamado.');
            redirect('chamados/detalhes/' . $_POST['id_chamado']);
        }
    }

    public function getNotificacoesUsuario()
    {
        $this->chamados_model->usuarioTemNotificacoes(id_usuario());
    }

}
