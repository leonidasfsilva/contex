<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Conecte extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Conecte_model');
    }

    public function index()
    {
        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('conecte/login');
        } else {
            redirect('conecte/painel');
        }

    }

    public function login()
    {
        if (!session_id() || !$this->session->userdata('conectado')) {
            $this->load->view('conecte/login');
        } else {
            redirect('conecte/painel');
        }

    }

    public function sair()
    {
        $this->session->sess_destroy();
        redirect('conecte');
    }

    public function verificarLogin()
    {
        $email = $this->input->post('email');
        $cpf = $this->input->post('cpf');

        $this->db->where('email', $email);
        $this->db->where('cpf', $cpf);
        $this->db->limit(1);
        $cliente = $this->db->get('clientes')->row();

        if (($cliente) > 0) {
            $dados = array(
                'nome' => $cliente->nome,
                'id_clientes' => $cliente->id_clientes,
                'conectado' => true
            );
            $this->session->set_userdata($dados);
            redirect(base_url() . 'conecte/painel');
        } else {
//            print_array('entrou aqui');
//            exit;
            $this->session->set_flashdata('erro', 'Dados de acesso inválidos, por favor tente novamente.');
            redirect(base_url() . 'conecte/login');
        }
    }

    public function painel()
    {
        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('conecte');
        }

        $data['menuPainel'] = 'Index';
        $data['compras'] = $this->Conecte_model->getLastCompras($this->session->userdata('id_clientes'));
        $data['os'] = $this->Conecte_model->getLastOs($this->session->userdata('id_clientes'));
        $data['output'] = 'conecte/painel';
        $this->load->view('conecte/template', $data);

    }

    public function conta()
    {

        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('conecte');
        }

        $data['menuConta'] = 'conta';
        $data['result'] = $this->Conecte_model->getDados();

        $data['output'] = 'conecte/conta';
        $this->load->view('conecte/template', $data);
    }

    public function editarDados()
    {

        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('conecte');
        }
        $id = $this->session->userdata('id_clientes');
        $data['menuConta'] = 'conta';

        if ($_POST['post'] == true) {

            $data = array(
                'nome' => $this->input->post('nome'),
                'cpf' => $this->input->post('cpf'),
                'telefone' => $this->input->post('telefone'),
                'email' => $this->input->post('email'),
                'logradouro' => $this->input->post('logradouro'),
                'numero' => $this->input->post('numero'),
                'bairro' => $this->input->post('bairro'),
                'cidade' => $this->input->post('cidade'),
                'uf' => $this->input->post('uf'),
                'cep' => $this->input->post('cep')
            );

            if ($this->Conecte_model->edit('clientes', $data, 'id_clientes', $id) == true) {
                $this->session->set_flashdata('sucesso', 'Dados alterados com sucesso!');
                redirect(base_url() . 'conecte/conta');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar alterar dados!');
                redirect(base_url() . 'conecte/conta');
            }
        }

        $data['result'] = $this->Conecte_model->getDados();

        $data['output'] = 'conecte/editar_dados';
        $this->load->view('conecte/template', $data);
    }

    public function compras()
    {

        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('conecte');
        }

        $data['menuVendas'] = 'vendas';
        $this->load->library('pagination');


        $config['base_url'] = base_url() . 'mine/compras/';
        $config['total_rows'] = $this->Conecte_model->count('vendas', $this->session->userdata('cliente_id'));
        $config['per_page'] = 10;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $data['results'] = $this->Conecte_model->getCompras('vendas', '*', '', $config['per_page'], $this->uri->segment(3), '', '', $this->session->userdata('cliente_id'));

        $data['output'] = 'conecte/compras';
        $this->load->view('conecte/template', $data);

    }

    public function os()
    {

        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('conecte');
        }

        $data['menuOs'] = 'os';
        $this->load->library('pagination');


        $config['base_url'] = base_url() . 'mine/os/';
        $config['total_rows'] = $this->Conecte_model->count('os', $this->session->userdata('cliente_id'));
        $config['per_page'] = 10;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $data['results'] = $this->Conecte_model->getOs('os', '*', '', $config['per_page'], $this->uri->segment(3), '', '', $this->session->userdata('cliente_id'));

        $data['output'] = 'conecte/os';
        $this->load->view('conecte/template', $data);
    }

    public function visualizarOs($id = null)
    {

        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('conecte');
        }

        $data['menuOs'] = 'os';
        $this->data['custom_error'] = '';
        $this->load->model('Mxcode_model');
        $this->load->model('os_model');
        $data['result'] = $this->os_model->getById($this->uri->segment(3));
        $data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $data['emitente'] = $this->mapos_model->getEmitente();

        if ($data['result']->idClientes != $this->session->userdata('cliente_id')) {
            $this->session->set_flashdata('error', 'Esta OS não pertence ao cliente logado.');
            redirect('conecte/painel');
        }

        $data['output'] = 'conecte/visualizar_os';
        $this->load->view('conecte/template', $data);

    }

    public function imprimirOs($id = null)
    {

        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('conecte');
        }

        $data['menuOs'] = 'os';
        $this->data['custom_error'] = '';
        $this->load->model('Mxcode_model');
        $this->load->model('os_model');
        $data['result'] = $this->os_model->getById($this->uri->segment(3));
        $data['produtos'] = $this->os_model->getProdutos($this->uri->segment(3));
        $data['servicos'] = $this->os_model->getServicos($this->uri->segment(3));
        $data['emitente'] = $this->mapos_model->getEmitente();

        if ($data['result']->idClientes != $this->session->userdata('cliente_id')) {
            $this->session->set_flashdata('error', 'Esta OS não pertence ao cliente logado.');
            redirect('conecte/painel');
        }

        $this->load->view('conecte/imprimirOs', $data);

    }

    public function visualizarCompra($id = null)
    {

        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('conecte');
        }

        $data['menuVendas'] = 'vendas';
        $data['custom_error'] = '';
        $this->load->model('Mxcode_model');
        $this->load->model('vendas_model');
        $data['result'] = $this->vendas_model->getById($this->uri->segment(3));
        $data['produtos'] = $this->vendas_model->getProdutos($this->uri->segment(3));
        $data['emitente'] = $this->mapos_model->getEmitente();

        if ($data['result']->clientes_id != $this->session->userdata('cliente_id')) {
            $this->session->set_flashdata('error', 'Esta OS não pertence ao cliente logado.');
            redirect('conecte/painel');
        }


        $data['output'] = 'conecte/visualizar_compra';
        $this->load->view('conecte/template', $data);
    }

    public function imprimirCompra($id = null)
    {

        if (!session_id() || !$this->session->userdata('conectado')) {
            redirect('conecte');
        }

        $data['menuVendas'] = 'vendas';
        $data['custom_error'] = '';
        $this->load->model('Mxcode_model');
        $this->load->model('vendas_model');
        $data['result'] = $this->vendas_model->getById($this->uri->segment(3));
        $data['produtos'] = $this->vendas_model->getProdutos($this->uri->segment(3));
        $data['emitente'] = $this->mapos_model->getEmitente();

        if ($data['result']->clientes_id != $this->session->userdata('cliente_id')) {
            $this->session->set_flashdata('error', 'Esta OS não pertence ao cliente logado.');
            redirect('conecte/painel');
        }


        $this->load->view('conecte/imprimirVenda', $data);
    }

    public function minha_ordem_de_servico($y = null, $when = null)
    {

        if (($y != null) && (is_numeric($y))) {

            // Do not forget this number -> 44023
            // function sending => y = (7653 * ID) + 44023
            // function recieving => x = (y - 44023) / 7653

            // Example ID = 2 | y = 59329

            $y = intval($y);
            $id = ($y - 44023) / 7653;


            $data['menuOs'] = 'os';
            $this->data['custom_error'] = '';
            $this->load->model('Mxcode_model');
            $this->load->model('os_model');
            $data['result'] = $this->os_model->getById($id);
            if ($data['result'] == null) {
                // Resposta em caso de não encontrar a ordem de serviço
                //$this->load->view('conecte/login');

            } else {

                $data['produtos'] = $this->os_model->getProdutos($id);
                $data['servicos'] = $this->os_model->getServicos($id);
                $data['emitente'] = $this->mapos_model->getEmitente();

                $this->load->view('conecte/minha_os', $data);

            }


        } else {
            // Resposta em caso de não encontrar a ordem de serviço
            //$this->load->view('conecte/');
        }

    }

    // Cadastro de OS pelo cliente
    public function adicionarOs()
    {

        $this->load->library('form_validation');

        $this->form_validation->set_rules('descricaoProduto', 'Descrição', 'required');
        $this->form_validation->set_rules('defeito', 'Defeito');
        $this->form_validation->set_rules('observacoes', 'Observações');


        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? true : false);
        } else {

            $id = null;
            $usuario = $this->db->query('SELECT usuarios_id, count(*) as down FROM os GROUP BY usuarios_id ORDER BY down LIMIT 1')->row();
            if ($usuario->usuarios_id == null) {
                $this->db->where('situacao', 1);
                $this->db->limit(1);
                $usuario = $this->db->get('usuarios')->row();

                if ($usuario->id_usuarios == null) {
                    $this->session->set_flashdata('error', 'Ocorreu um erro ao cadastrar a ordem de serviço, por favor contate o administrador do sistema.');
                    redirect('conecte/os');
                } else {
                    $id = $usuario->id_usuarios;
                }
            } else {
                $id = $usuario->usuarios_id;
            }

            $data = array(
                'dataInicial' => date('Y-m-d'),
                'clientes_id' => $this->session->userdata('cliente_id'), //set_value('idCliente'),
                'usuarios_id' => $id, //set_value('idUsuario'),
                'dataFinal' => date('Y-m-d'),
                'descricaoProduto' => $this->input->post('descricaoProduto'),
                'defeito' => $this->input->post('defeito'),
                'status' => 'Aberto',
                'observacoes' => set_value('observacoes'),
                'faturado' => 0
            );

            if (is_numeric($id = $this->Conecte_model->add('os', $data, true))) {
                $this->session->set_flashdata('success', 'OS adicionada com sucesso!');
                redirect('conecte/detalhesOs/' . $id);

            } else {

                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro.</p></div>';
            }
        }

        $data['output'] = 'conecte/adicionarOs';
        $this->load->view('conecte/template', $data);
    }

    public function detalhesOs($id = null)
    {
        if (is_numeric($id) && $id != null) {

            $this->load->model('Mxcode_model');
            $this->load->model('os_model');

            $this->data['result'] = $this->os_model->getById($id);
            $this->data['produtos'] = $this->os_model->getProdutos($id);
            $this->data['servicos'] = $this->os_model->getServicos($id);
            $this->data['anexos'] = $this->os_model->getAnexos($id);

            if ($this->data['result']->idClientes != $this->session->userdata('cliente_id')) {
                $this->session->set_flashdata('error', 'Esta OS não pertence ao cliente logado.');
                redirect('conecte/painel');
            }

            $this->data['output'] = 'conecte/detalhes_os';
            $this->load->view('conecte/template', $this->data);
        } else {
            echo "teste";
        }
    }

    // método para clientes se cadastrarem
    public function cadastro()
    {

        $this->load->model('clientes_model', '', true);
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('clientes') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $data = array(
                'nomeCliente' => set_value('nomeCliente'),
                'documento' => set_value('documento'),
                'telefone' => set_value('telefone'),
                'celular' => $this->input->post('celular'),
                'email' => set_value('email'),
                'rua' => set_value('rua'),
                'numero' => set_value('numero'),
                'bairro' => set_value('bairro'),
                'cidade' => set_value('cidade'),
                'estado' => set_value('estado'),
                'cep' => set_value('cep'),
                'dataCadastro' => date('Y-m-d')
            );

            if ($this->clientes_model->add('clientes', $data) == true) {
                $this->session->set_flashdata('success', 'Cliente adicionado com sucesso!');
                redirect(base_url() . 'conecte');
            } else {
                $this->session->set_flashdata('success', 'Cliente adicionado com sucesso!');
            }
        }
        $data = '';
        $this->load->view('conecte/cadastro', $data);
    }
}

/* End of file conecte.php */
/* Location: ./application/controllers/conecte.php */
