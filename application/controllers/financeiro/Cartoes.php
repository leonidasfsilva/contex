<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cartoes extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }
    }

    public function index()
    {
        $this->cartoes();
    }

    //MODULO DE CARTOES
    public function cartoes()
    {
        $cartoes = $this->cartoes_model->getCartoesUsuario(getUserId());

        $data['results'] = $cartoes;
        $data['menuFinanceiro'] = true;
        $data['view'] = 'cartoes/cartoes';
        $this->load->view('tema/topo', $data);
    }

    public function adicionar()
    {
        if ($_POST) {
//            $n_cartao = explode(" ", trim($_POST['number']));
            $n_cartao = str_replace(' ', '', $_POST['number']);
            $validade = str_replace(' ', '', $_POST['expiry']);

            $bandeira = padronizarString($_POST['bandeira']);
            $final = $n_cartao[3];
//            print_array_exit($_POST);

            $data = array(
                'numero' => encriptar($_POST['number']),
                'nome' => padronizarString($_POST['name']),
                'validade' => $validade,
                'cvc' => encriptar($_POST['cvc']),
                'bandeira' => padronizarString($_POST['bandeira']),
                'id_usuario' => getUserId()
            );
            $existe_cartao = $this->consultaCartoesUsuario();

            if ($this->cartoes_model->add('cartoes', $data)) {
                $last_id = $this->db->insert_id('cartoes');
                if (!$existe_cartao) {
                    $this->associaCartaoFatura($last_id);
                }
                $this->session->set_flashdata('sucesso', 'Cartão cadastrado com sucesso!');
                redirect('financeiro/cartoes');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar cadastrar cartão.');
                redirect('financeiro/cartoes');
            }
        } else {
            $data['menuFinanceiro'] = true;
            $data['view'] = 'cartoes/adicionar';
            $this->load->view('tema/topo', $data);
        }
    }

    public function adicional($id_cartao = null)
    {
        if ($_POST) {
//            $n_cartao = explode(" ", trim($_POST['number']));
            $n_cartao = str_replace(' ', '', $_POST['number']);
            $validade = str_replace(' ', '', $_POST['expiry']);

            $bandeira = padronizarString($_POST['bandeira']);
            $final = $n_cartao[3];

            $data = array(
                'numero' => encriptar($_POST['number']),
                'nome' => padronizarString($_POST['name']),
                'validade' => $validade,
                'cvc' => encriptar($_POST['cvc']),
                'bandeira' => padronizarString($_POST['bandeira']),
                'id_usuario_titular' => getUserId(),
                'id_usuario' => $_POST['id_usuario'],
                'id_cartao_titular' => $_POST['id_cartao'],
                'adicional' => 1,
            );
            $existe_cartao = $this->consultaCartoesUsuario();

            if ($this->cartoes_model->add('cartoes', $data)) {
                $last_id = $this->db->insert_id('cartoes');

                if (!$existe_cartao) {
                    $this->associaCartaoFatura($last_id);
                }

                $data = array(
                    'possui_adicional' => 1
                );

                $this->cartoes_model->edit('cartoes', $data, 'id_cartao', $_POST['id_cartao']);

                $this->session->set_flashdata('sucesso', 'Cartão adicional gerado com sucesso!');
                redirect('financeiro/cartoes');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar gerar cartão adicional.');
                redirect('financeiro/cartoes');
            }
        } else {
            if (!$id_cartao) {
                $this->session->set_flashdata('erro', 'Método não permitido.');
                redirect('financeiro/cartoes');
            }
            $cartao_titular = $this->cartoes_model->cartaoPertenceUsuario(getUserId(), $id_cartao);

            if ($cartao_titular) {
                if ($cartao_titular->adicional == 1) {
                    $this->session->set_flashdata('erro', 'Este cartão não é elegível para gerar um cartão adicional, apenas cartões titulares podem gerar cartões adicionais.');
                    redirect('financeiro/cartoes');
                }
                $data['cartao'] = $cartao_titular;
                $data['menuFinanceiro'] = true;
                $data['view'] = 'cartoes/adicional';
                $this->load->view('tema/topo', $data);
            } else {
                $this->session->set_flashdata('erro', 'Cartão informado não pertence ao usuário.');
                redirect('financeiro/cartoes');
            }
        }
    }

    public function editar($id_cartao = null, $adicional = null)
    {
        if (!$id_cartao) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('financeiro/cartoes');
        }

        if ($_POST) {
//            $n_cartao = explode(" ", trim($_POST['number']));
            $n_cartao = str_replace(' ', '', $_POST['number']);
            $validade = str_replace(' ', '', $_POST['expiry']);

            $bandeira = padronizarString($_POST['bandeira']);
            $final = $n_cartao[3];
//            print_array_exit($_POST);

            $data = array(
                'numero' => encriptar($_POST['number']),
                'nome' => padronizarString($_POST['name']),
                'validade' => $validade,
                'cvc' => encriptar($_POST['cvc']),
                'bandeira' => padronizarString($_POST['bandeira']),
            );

            if ($this->cartoes_model->edit('cartoes', $data, 'id_cartao', $_POST['id_cartao'])) {
                $this->session->set_flashdata('sucesso', 'Dados do cartão alterados com sucesso!');
                redirect('financeiro/cartoes');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar alterar dados do cartão');
                redirect('financeiro/cartoes');
            }
        } else {
            $cartao = $this->cartoes_model->getDetalhesCartao($id_cartao);

            if (!$this->cartoes_model->verificaCartaoAtivo($id_cartao)) {
                $this->session->set_flashdata('erro', 'Cartão não encontrado');
                redirect('financeiro/cartoes');
            }

            if ($cartao->adicional) {
                if ($cartao->id_usuario_titular != getUserId()) {
                    $this->session->set_flashdata('erro', 'Você não tem permissão para editar os dados deste cartão, solicite a alteração dos dados ao titular.');
                    redirect('financeiro/cartoes');
                }
            }
            $data['cartao'] = $cartao;
            $data['menuFinanceiro'] = true;
            $data['view'] = 'cartoes/editar';
            $this->load->view('tema/topo', $data);
        }
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dFaturas')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir cartões.');
            redirect(base_url());
        }

        if ($_POST) {
            $id_cartao = $_POST['id_cartao'];
        } else {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('financeiro/cartoes');
        }

        $cartao = $this->cartoes_model->cartaoExistente($id_cartao);
        if ($cartao) {
            if ($cartao->adicional == 1) {
                if ($cartao->id_usuario_titular != getUserId()) {
                    $this->session->set_flashdata('erro', 'Você não pode excluir este cartão, apenas o titular emissor pode exclui-lo.');
                    redirect('financeiro/cartoes');
                }
            } else {
                if ($cartao->possui_adicional == 1) {
                    $this->session->set_flashdata('erro', 'Não é possível excluir cartões que possuam cartões adicionais associados.');
                    redirect('financeiro/cartoes');
                }
            }
        } else {
            $this->session->set_flashdata('erro', 'Cartão não encontrado.');
            redirect('financeiro/cartoes');
        }

        //consulta se o cartão selecionado possui faturas associadas
        $fatura = $this->cartoes_model->consultaFaturasCartao($id_cartao);

        if ($fatura) {
            $this->session->set_flashdata('erro', 'Não é possível excluir cartões que possuam faturas associadas.');
            redirect('financeiro/cartoes');
        }

        $data = array(
            'status' => 0,
        );

        if ($this->cartoes_model->edit('cartoes', $data, 'id_cartao', $id_cartao)) {
            $this->session->set_flashdata('sucesso', 'Cartão excluído com sucesso!');
            redirect('financeiro/cartoes');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar excluir o cartão.');
            redirect('financeiro/cartoes');
        }
    }

    //Funcao para consultar se ja existe um cartao associado ao usuario:
    //se existir, retorna TRUE
    //se não existir, retorna FALSE
    public function consultaCartoesUsuario($id_cartao = null)
    {
        $existe_cartao = $this->cartoes_model->getCartoesUsuario(getUserId(), $id_cartao);
        if ($existe_cartao) {
            return true;
        } else {
            return false;
        }
    }

    //Funcao para associar o primeiro cartao cadastrado a todas as faturas já existentes do usuario
    //esta funçao somente deve ser executada caso o usuário NÃO possua nenhum cartão cadastrado
    //(esta função é somente para usuários que já utilizavam o modulo de Faturas antes do modulo de Cartões ser lançado,
    // para os novos usuários, o ciclo segue normalmente)
    public function associaCartaoFatura($id_cartao)
    {
        $data = array(
            'id_cartao' => $id_cartao
        );
        $this->fatura_model->edit('faturas', $data, 'id_usuario', getUserId());
    }

    //Funcao para consultar se existe algum usuario no sistema com o CPF informado.
    public function consultarUsuarioCPF()
    {
        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Não é permitido acesso direto à URLs de serviços.');
            redirect('/');
        }

        $usuario = $this->cartoes_model->consultarUsuario($_POST['cpf']);

        if ($usuario) {
            $data = array(
                'result' => true,
                'retorno' => $usuario
            );
            echo json_encode($data);
        } else {
            $data = array(
                'result' => false,
                'retorno' => $_POST['cpf']
            );
            echo json_encode($data);
        }
    }

}
