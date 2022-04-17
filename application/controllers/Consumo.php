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
    }

    public function index()
    {
        $configs = $this->consumo_model->getConfigsConsumo(getUserId());
        $inicio_medicao = explode('-', $configs->data_leitura);
        $ano = $inicio_medicao[0];
        $mes = $inicio_medicao[1] + 1;
        $dia = $inicio_medicao[2];


        $data['referencia'] = padronizarString(strftime('%B / %Y', strtotime($dia . '-' . $mes . '-' . $ano)));
        $data['configs'] = $configs;
        $data['results'] = $this->consumo_model->getConsumosUsuario(getUserId());
        $data['menuConsumo'] = true;
        $data['view'] = 'consumo/consumo';
        $this->load->view('tema/topo', $data);
    }

    public function configuracoes()
    {
        if ($_POST) {
            if ($_POST['data_leitura']) {
                $data_leitura = explode('/', $_POST['data_leitura']);
                $data_leitura = $data_leitura[2] . '-' . $data_leitura[1] . '-' . $data_leitura[0];
            } else {
                $data_leitura = date('Y-m-d');
            }

            if ($_POST['valor_kwh']) {
                $valor = str_replace(array('.', ','), array('', '.'), $_POST['valor_kwh']);
            } else {
                $valor = '0.8961';
            }

            $data = array(
                'valor_kwh' => $valor,
                'leitura_inicial' => $_POST['leitura_inicial'],
                'data_leitura' => $data_leitura,
                'id_usuario' => getUserId(),
            );
            // verifica se o usuário já possui configurações de consumo cadastradas
            if (!$this->consumo_model->getConfigsConsumo(getUserId())) {
                if ($this->consumo_model->add('configs_consumo_assoc', $data)) {
                    $this->session->set_flashdata('sucesso', 'Configurações de consumo cadastradas com sucesso!');
                    redirect('consumo');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao tentar cadastrar configurações de consumo');
                    redirect('consumo/configuracoes');
                }
            } else {
                if ($this->consumo_model->edit('configs_consumo_assoc', $data, 'id_usuario', getUserId())) {
                    $this->session->set_flashdata('sucesso', 'Configurações de consumo atualizadas com sucesso!');
                    redirect('consumo');
                } else {
                    $this->session->set_flashdata('erro', 'Erro ao atualizar configurações de consumo');
                    redirect('consumo/configuracoes');
                }
            }
        }
        $data['configs'] = $this->consumo_model->getConfigsConsumo(getUserId());
        $data['view'] = 'consumo/configuracoes';
        $data['menuConsumo'] = true;
        $this->load->view('tema/topo', $data);
    }

    public function registrar()
    {
        $leitura = $_POST['leitura'];
        $data_leitura = $_POST['data_leitura'];
        $configs = $this->consumo_model->getConfigsConsumo(getUserId());

        if ($consumo_usuario = $this->consumo_model->getConsumoUsuario(getUserId())) {
            $ultima_leitura = $consumo_usuario->leitura_atual;
        } else {
            $ultima_leitura = $configs->leitura_inicial;
        }

        if ($_POST['data_leitura']) {
            $data_leitura = explode('/', $_POST['data_leitura']);
            $ano = $data_leitura[2];
            $mes = $data_leitura[1];
            $dia = $data_leitura[0];
            $data_leitura = $data_leitura[2] . '-' . $data_leitura[1] . '-' . $data_leitura[0];
        } else {
            $data_leitura = date('Y-m-d');
            $data_leitura = explode('-', $data_leitura);
            $ano = $data_leitura[0];
            $mes = $data_leitura[1];
            $dia = $data_leitura[2];
            $data_leitura = $data_leitura[0] . '-' . $data_leitura[1] . '-' . $data_leitura[2];
        }

        //formula de calculo para obter o valor da conta de luz
        $consumo = $leitura - $ultima_leitura;
        $valor = $configs->valor_kwh * $consumo;

        $data = array(
            'valor' => $valor,
            'leitura_atual' => $leitura,
            'leitura_anterior' => $ultima_leitura,
            'id_usuario' => getUserId(),
            'consumo' => $consumo,
            'data_leitura' => $data_leitura,
            'mes_referencia' => $mes,
            'ano_referencia' => $ano,
        );

        if ($this->consumo_model->add('consumo', $data)) {
            $this->session->set_flashdata('sucesso', 'Consumo registrado com sucesso!');
            redirect('consumo');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar registrar consumo');
            redirect('consumo');
        }
    }

    public function editar()
    {
        $id = $_POST['id'];
        $leitura = $_POST['leitura'];
        $data_leitura = $_POST['data_leitura'];
        $configs = $this->consumo_model->getConfigsConsumo(getUserId());
        $consumo_atual = $this->consumo_model->getConsumoByID($id);

        if ($_POST['data_leitura']) {
            $data_leitura = explode('/', $_POST['data_leitura']);
            $ano = $data_leitura[2];
            $mes = $data_leitura[1];
            $dia = $data_leitura[0];
            $data_leitura = $data_leitura[2] . '-' . $data_leitura[1] . '-' . $data_leitura[0];
        } else {
            $data_leitura = date('Y-m-d');
            $data_leitura = explode('-', $data_leitura);
            $ano = $data_leitura[0];
            $mes = $data_leitura[1];
            $dia = $data_leitura[2];
            $data_leitura = $data_leitura[0] . '-' . $data_leitura[1] . '-' . $data_leitura[2];
        }

        //formula de calculo para obter o valor da conta de luz
        $consumo = $leitura - $consumo_atual->leitura_anterior;
        $valor = $configs->valor_kwh * $consumo;

        $data = array(
            'valor' => $valor,
            'leitura_atual' => $leitura,
            'leitura_anterior' => $consumo_atual->leitura_anterior,
            'id_usuario' => getUserId(),
            'consumo' => $consumo,
            'data_leitura' => $data_leitura,
            'mes_referencia' => $mes,
            'ano_referencia' => $ano,
        );

        if ($this->consumo_model->edit('consumo', $data, 'id', $id)) {
            $this->session->set_flashdata('sucesso', 'Consumo atualizado com sucesso!');
            redirect('consumo');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar atualizar consumo');
            redirect('consumo');
        }
    }

    public function pesquisar()
    {
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }

        $termo = $this->input->get('termo');

        $data['results'] = $this->mxcode_model->pesquisar($termo, getUserId());
        $this->data['produtos'] = $data['results']['produtos'];
        $this->data['servicos'] = $data['results']['servicos'];
        $this->data['os'] = $data['results']['os'];
        $this->data['clientes'] = $data['results']['clientes'];
        $this->data['view'] = 'mxcode/pesquisa';
        $this->load->view('tema/topo', $this->data);
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
            $chamadoUsuario = $this->chamados_model->verificaChamadoPertenceUsuario(getUserId());
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
                'id_usuario' => getUserId(),
                'id_chamado' => $_POST['id_chamado'],
            );
            $data2 = array(
                'notifica_usuario' => 1,
            );
            $this->chamados_model->edit('chamados', $data2, 'id_chamado', $_POST['id_chamado']);
        } else {
            $data = array(
                'resposta' => $_POST['resposta'],
                'id_usuario' => getUserId(),
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

    public function excluir()
    {
        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido');
            redirect('consumo');
        }

        $data = array(
            'status' => 0,
        );

        if ($this->chamados_model->edit('consumo', $data, 'id', $_POST['id']) == true) {
            $this->session->set_flashdata('sucesso', 'Consumo excluído com sucesso!');
            redirect('consumo' . $_POST['id_chamado']);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir consumo');
            redirect('consumo');
        }
    }

    public function getNotificacoesUsuario()
    {
        $this->chamados_model->usuarioTemNotificacoes(getUserId());
    }
}
