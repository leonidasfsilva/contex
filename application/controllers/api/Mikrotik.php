<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Mikrotik extends CI_Controller
{
    /**
     * author: Leônidas Ferreira
     * email: leonidas.f.silva@hotmail.com
     *
     */

    protected ?string $token         = null;
    protected ?array  $request       = null;
    protected bool    $authenticated = false;

    public function __construct()
    {
        parent::__construct();

        if ($_GET) {
            if ($this->input->get('token')) {
                $this->token = $this->input->get('token');
            }
        }

        if ($_POST) {
            if ($this->input->post('token')) {
                $this->token = $this->input->post('token');
            }
        }

        $this->request = json_decode(file_get_contents('php://input'), true);

        if ($this->request && is_array($this->request)) {
            if (isset($this->request['token'])) {
                $this->token = $this->request['token'];
            }
        }

        if ($this->token) {
            if ($this->checkToken($this->token)) {
                $this->authenticated = true;
                gravaLog(null, null, null, 'User agent autenticado com sucesso', getenv("REMOTE_ADDR"));
            }
        }
    }

    public function index()
    {
        if (!$this->token || !$this->authenticated) {
            gravaLog(null, null, null, 'Unauthorized: Tentativa de acesso recusada ao index da API', getenv("REMOTE_ADDR"));
            return $this->response(
                ['response' => 'Error 401 Unauthorized'],
                401
            );
        }
        return $this->getInfo();
    }

    public function getInfo()
    {
        if (!$this->token || !$this->authenticated) {
            gravaLog(null, null, null, 'Unauthorized: Tentativa de acesso recusada de getInfo da API', getenv("REMOTE_ADDR"));
            return $this->response(
                ['response' => 'Error 401 Unauthorized'],
                401
            );
        }

        $response = [
            'appVersion:' => APP_VERSION,
            'phpServer:'  => phpversion(),
        ];

        return $this->response($response);
    }

    public function getToken()
    {
        if (!$this->token || !$this->authenticated) {
            gravaLog(null, null, null, 'Unauthorized: Tentativa de acesso recusada de getToken da API', getenv("REMOTE_ADDR"));
            return $this->response(
                ['response' => 'Error 401 Unauthorized'],
                401
            );
        }

        $token = str_shuffle(
            '1234567890' .
            'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvXxYyWwZz' .
            'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvXxYyWwZz' .
            '1234567890'
        );

        $response = [
            'token' => $token
        ];

        return $this->response($response);
    }

    public function sendEmail()
    {
        if (!$this->token || !$this->authenticated) {
            gravaLog(null, null, null, 'Unauthorized: Tentativa recusada de envio de email de relatório Mikrotik', getenv("REMOTE_ADDR"));
            return $this->response(
                ['response' => 'Error 401 Unauthorized'],
                401
            );
        }

        $request = $this->request;

        try {
            $email    = $request['to'];
            $subject  = $request['subject'];
            $template = $this->buildEmailTemplate($request);
            $_from    = "mikrotik@mxcode.net";
            $_headers = "MIME-Version: 1.0\r\n";
            $_headers .= "Content-type: text/html; charset=utf-8\r\n";
            $_headers .= "From: " . $_from . "\r\n";
            // $_headers .= "X-Priority: 1\r\n";

            mail($email, $subject, $template, $_headers, "-f " . $_from);

            $response = [
                'response'      => '200 OK',
                'remoteAddress' => $_SERVER['REMOTE_ADDR'] ?? null,
            ];

            gravaLog(null, null, $email, 'Email de relatório Mikrotik enviado com sucesso', getenv("REMOTE_ADDR"));
            return $this->response($response);
        } catch (Exception $e) {
            return $this->response(
                ['response' => 'Error 400 Bad Request'],
                400
            );
        }
    }

    private function checkToken($token)
    {
        $storagedToken = $this->mxcode_model->getTokenByToken($token)->result();

        if ($storagedToken) {
            return $storagedToken;
        }
        return false;
    }

    private function response($response = [], $code = 200)
    {
        if (!is_array($response)) {
            return false;
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($code)
            ->set_output(json_encode($response, JSON_PRETTY_PRINT));

    }

    private function buildEmailTemplate($request = null)
    {
        $formattedDateTime = date("d/m/Y h:i");
        $ip                = getenv("REMOTE_ADDR");
        $navegador         = $_SERVER['HTTP_USER_AGENT'];

        if ($request && is_array($request)) {
            $body              = $request['body'];
            $time              = date('H:i');
            $formattedDateTime = date('d/m/Y');
            $formattedDateTime = sprintf('%s - %s', $formattedDateTime, $time);
        }

        return '
                <html>
                <head>
                <style>
                table {
                    font-family: "Arial", sans-serif;
                    font-size: 10pt;
                }
                #inner_table {
                    border: 2px solid lightgray;
                    border-radius: 10px;
                }
                td {
                    padding: 0px 20px 20px 20px;
                    text-align: left;    
                }
                </style>
                </head>
                <body>
                <table>
                    <tbody>
                        <tr>
                        <td>
                            <table>
                            <tr>
                                <td colspan="2" style="border-bottom: 4px solid #0098da; padding: 20px 20px 20px 20px;">
                                <img src="' . base_url() . 'assets/img/mikrotik_logo.png" alt="MikroTik Home Server" style="width:200px;">
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 20px">
                                <span style="font-size: 12pt;">Prezado usuário,</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <span>' . $body . '</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h4 style="font-weight: bold">Localização e horário do evento</h4>
                                    <span style="font-weight: bold">IP:</span> <a target="_blank" href="https://whatismyipaddress.com/ip/' . $ip . '"> ' . $ip . '</a>
                                    <br />
                                    <span style="font-weight: bold">User Agent:</span> ' . $navegador . '
                                    <br />
                                    <span style="font-weight: bold">Data e hora:</span> ' . $formattedDateTime . '
                                    <br /> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                <span>Atenciosamente,</span>
                                <br>
                                <span style="font-size: 12pt"><strong>Equipe MXCODE Sistemas</strong></span>
                                <br>
                                <a href="' . base_url() . '" target="_blank"><p><strong>' . base_url() . '</strong></a><br>CONTEX - Sistema de Gestão</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="border-top: 2px dotted #0098da; padding-top: 20px">
                                <p style="font-size:10pt; color: gray">
                                Não é necessário responder este e-mail, mensagem automática.
                                <p>
                                </td>
                            </tr>
                            </table>
                        </td>
                        </tr>
                    </tbody>
                </table>                
                </body>
                </html>
            ';
    }
}