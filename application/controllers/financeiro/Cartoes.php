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
        $data['results'] = $this->cartoes_model->getCartoesUsuario(id_usuario());
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
                'numero' => base64_encode($_POST['number']),
                'nome' => padronizarString($_POST['name']),
                'validade' => $validade,
                'cvc' => base64_encode($_POST['cvc']),
                'bandeira' => padronizarString($_POST['bandeira']),
                'id_usuario' => id_usuario()
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

    public function editar($id_cartao = null)
    {
        if ($id_cartao == null) {
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
                'numero' => base64_encode($_POST['number']),
                'nome' => padronizarString($_POST['name']),
                'validade' => $validade,
                'cvc' => base64_encode($_POST['cvc']),
                'bandeira' => padronizarString($_POST['bandeira']),
            );

            if ($this->cartoes_model->edit('cartoes', $data, 'id_cartao', $_POST['id_cartao'])) {
                $this->session->set_flashdata('sucesso', 'Dados do cartão alterados com sucesso!');
                redirect('financeiro/cartoes');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar alterar dados do cartão.');
                redirect('financeiro/cartoes');
            }
        } else {
            $data['cartao'] = $this->cartoes_model->getDetalhesCartao($id_cartao);

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

        //consulta se o cartão selecionado possui faturas associadas
        $fatura = $this->cartoes_model->consultaFaturasCartao($id_cartao);

        if ($fatura) {
            $this->session->set_flashdata('erro', 'Não é possível excluir este cartão, existem faturas associadas.');
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
    public function consultaCartoesUsuario()
    {
        $existe_cartao = $this->cartoes_model->getCartoesUsuario(id_usuario());
        if ($existe_cartao) {
            return true;
        } else {
            return false;
        }
    }

    //Funcao para associar o primeiro cartao cadastrado a todas as faturas do usuario
    //esta funçao somente deve ser executada caso o usuário NÃO possua nenhum cartão cadastrado
    public function associaCartaoFatura($id_cartao)
    {
        $data = array(
            'id_cartao' => $id_cartao
        );
        $this->fatura_model->edit('faturas', $data, 'id_usuario', id_usuario());
    }

    //MODULO DE RETORNO DE FILTROS POR PERIODO
    protected function getThisYear()
    {

        $dias = date("z");
        $primeiro = date("Y-m-d", strtotime("-" . ($dias) . " day"));
        $ultimo = date("Y-m-d", strtotime("+" . (364 - $dias) . " day"));
        return array($primeiro, $ultimo);

    }

    protected function getThisWeek()
    {

        return array(date("Y/m/d", strtotime("last sunday", strtotime("now"))), date("Y/m/d", strtotime("next saturday", strtotime("now"))));
    }

    protected function getLastThreeDays()
    {

        return array(date("Y-m-d", strtotime("-3 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastFiveDays()
    {

        return array(date("Y-m-d", strtotime("-5 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastSevenDays()
    {

        return array(date("Y-m-d", strtotime("-7 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastFifteenDays()
    {

        return array(date("Y-m-d", strtotime("-15 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastTirthyDays()
    {

        return array(date("Y-m-d", strtotime("-30 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastSixtyDays()
    {

        return array(date("Y-m-d", strtotime("-60 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getLastNinetyDays()
    {

        return array(date("Y-m-d", strtotime("-90 day", strtotime("now"))), date("Y-m-d", strtotime("now")));
    }

    protected function getThisMonth()
    {

        $mes = date('m');
        $ano = date('Y');
        $qtdDiasMes = date('t');
        $inicia = $ano . "-" . $mes . "-01";

        $ate = $ano . "-" . $mes . "-" . $qtdDiasMes;
        return array($inicia, $ate);
    }
}
