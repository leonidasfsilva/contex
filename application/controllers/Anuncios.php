<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Anuncios extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('mxcode/login');
        }
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para gerenciar anúncios.');
            redirect(base_url());
        }

        $this->load->helper('file');
        $this->load->library('upload');
        $this->load->library('image_lib');
    }

    public function index()
    {
        $data['menuConfiguracoes'] = true;
        $data['results'] = $this->anuncios_model->getAnuncios();
        $data['view'] = 'anuncios/anuncios';
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

    public function adicionar()
    {
        if ($_POST) {
            $exibir_rodape = $_POST['exibir_rodape'];
            $exibir_botao = $_POST['exibir_botao'];
            $descricao = $_POST['descricao'];
            $titulo = $_POST['titulo'];
            $cabecalho = $_POST['cabecalho'];
            $rotulo_botao = $_POST['rotulo_botao'];
            $link_botao = $_POST['link_botao'];

            $data = array(
                'descricao' => $descricao,
                'titulo' => $titulo,
                'cabecalho' => $cabecalho,
                'exibir_rodape' => $exibir_rodape,
                'exibir_botao' => $exibir_botao,
                'rotulo_botao' => $rotulo_botao,
                'link_botao' => $link_botao,
                'estilo' => $_POST['estilo'],
            );

            if ($this->anuncios_model->add('anuncios', $data) == true) {
                $this->session->set_flashdata('sucesso', 'Anúncio cadastrado com sucesso!');
                redirect('anuncios');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar cadastrar anúncio.');
                redirect('anuncios');
            }

        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'cPermissao')) {
            $this->session->set_flashdata('erro', 'Você não tem permissão para cadastrar anúncios.');
            redirect(base_url());
        }

        $data['menuConfiguracoes'] = true;
        $data['view'] = 'anuncios/adicionar';
        $this->load->view('tema/topo', $data);
    }

    public function editar($id_anuncio = null)
    {
        if ($_POST) {
            $data = array(
                'exibir_rodape' => $_POST['exibir_rodape'],
                'exibir_botao' => $_POST['exibir_botao'],
                'rotulo_botao' => $_POST['rotulo_botao'],
                'link_botao' => $_POST['link_botao'],
                'cabecalho' => $_POST['cabecalho'],
                'titulo' => $_POST['titulo'],
                'descricao' => $_POST['descricao'],
                'estilo' => $_POST['estilo'],
            );

            if ($this->anuncios_model->edit('anuncios', $data, 'id_anuncio', $id_anuncio) == true) {
                $this->session->set_flashdata('sucesso', 'Anúncio alterado com sucesso!');
                redirect('anuncios/editar/' . $id_anuncio);
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar alterar anúncio.');
                redirect('anuncios/editar/' . $id_anuncio);
            }

        } else {
            if ($id_anuncio == null) {
                $this->session->set_flashdata('erro', 'Método não permitido.');
                redirect('anuncios');
            }

            $anuncio = $this->anuncios_model->getDetalhesAnuncio($id_anuncio);

            if (!$anuncio) {
                $this->session->set_flashdata('erro', 'Anúncio não encontrado.');
                redirect('anuncios');
            }

            $data['result'] = $anuncio;
            $data['view'] = 'anuncios/editar';
            $this->load->view('tema/topo', $data);
        }
    }

    public function configurar()
    {
        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('anuncios');
        }

        if ($_POST['data_expiracao'] != null) {
            $data_expiracao = explode('/', $_POST['data_expiracao']);
            $data_expiracao = $data_expiracao[2] . '-' . $data_expiracao[1] . '-' . $data_expiracao[0];
        } else {
            $data_expiracao = null;
        }

        if ($_POST['habilitado'] == 1) {
            $habilitado = 1;
        } else {
            $habilitado = 0;
        }

        if ($_POST['direcionado'] == 1) {
            $direcionado = 1;
        } else {
            $direcionado = 0;
        }

        $data = array(
            'habilitado' => $habilitado,
            'direcionado' => $direcionado,
            'id_usuario' => $_POST['id_usuario'] ? $_POST['id_usuario'] : null,
            'nome_usuario' => $_POST['nome_usuario'] ? $_POST['nome_usuario'] : null,
            'data_expiracao' => $data_expiracao,
        );

        if ($this->anuncios_model->edit('anuncios', $data, 'id_anuncio', $_POST['id_anuncio'])) {
            $this->session->set_flashdata('sucesso', 'Anúncio configurado com sucesso!');
            redirect('anuncios');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao tentar configurar anúncio.');
            redirect('anuncios');
        }
    }

    public function copiar()
    {
        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('anuncios');
        }

        $id_anuncio = $_POST['id_anuncio'];
        // Get the columns
        $cols = array();
        $result = $this->db->query("SHOW COLUMNS FROM anuncios"); // Change table name
//        print_array_exit($result->result_array());

        while ($r = $result->unbuffered_row('array')) {
            if (!in_array($r["Field"], array("id_anuncio"))) { // Edit array with any column names you want to exclude
                $cols[] = $r["Field"];
            }
        }

        // Build and do the insert
        $result = $this->db->query("SELECT * FROM anuncios WHERE id_anuncio = $id_anuncio"); // Change table name and add selection criteria
        while ($r = $result->unbuffered_row('array')) {

            $insertSQL = "INSERT INTO anuncios (" . implode(", ", $cols) . ") VALUES ("; // Change table name
            $count = count($cols);

            foreach ($cols as $counter => $col) {
                // This is where you can add any code to change the value of existing columns
                $insertSQL .= $this->db->escape($r[$col]);
                if ($counter < ($count - 1)) {
                    $insertSQL .= ", ";
                }
            } // END foreach

            $insertSQL .= ");";

            $this->db->query($insertSQL);
            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('sucesso', 'Anúncio copiado com sucesso!');
                redirect('anuncios');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar copiar anúncio.');
                redirect('anuncios');
            }
        } // END while
    }

    public function excluir()
    {
        if (!$_POST) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('anuncios');
        }

        $id_anuncio = $_POST['id_anuncio'];

        if ($id_anuncio == null || !is_numeric($id_anuncio)) {
            $this->session->set_flashdata('erro', 'Método não permitido.');
            redirect('anuncios');
        } else {
            $data = array(
                'status' => 0
            );

            if ($this->anuncios_model->edit('anuncios', $data, 'id_anuncio', $id_anuncio)) {
                $this->session->set_flashdata('sucesso', 'Anúncio excluído com sucesso!');
                redirect('anuncios');
            } else {
                $this->session->set_flashdata('erro', 'Erro ao tentar excluir anúncio.');
                redirect('anuncios');
            }
        }
    }

    public function autoCompleteUsuario()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->anuncios_model->autoCompleteUsuario($q);
        }
    }

}
