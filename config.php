<?php
// includes/config.php
session_start();

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'petrol_check');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configurações do sistema
define('SITE_NAME', 'PETROL CHECK');
define('SITE_URL', 'http://localhost/petrolcheck');

// Conexão com banco de dados
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Erro na conexão com a base de dados: " . $e->getMessage());
}

// Funções auxiliares
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isGestor() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'gestor';
}

function isTecnico() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'tecnico';
}

// Função para gerar badges de status
function getStatusBadge($status) {
    $statuses = [
        'pendente' => ['class' => 'status-pendente', 'text' => 'Pendente'],
        'analise' => ['class' => 'status-analise', 'text' => 'Em Análise'],
        'aprovado' => ['class' => 'status-aprovado', 'text' => 'Aprovado'],
        'rejeitado' => ['class' => 'status-rejeitado', 'text' => 'Rejeitado'],
        'concluido' => ['class' => 'status-concluido', 'text' => 'Concluído'],
        'realizada' => ['class' => 'status-concluido', 'text' => 'Realizada'],
        'cancelada' => ['class' => 'status-rejeitado', 'text' => 'Cancelada'],
        'atribuida' => ['class' => 'status-analise', 'text' => 'Atribuída'],
        'em_andamento' => ['class' => 'status-analise', 'text' => 'Em Andamento'],
        'ativo' => ['class' => 'status-aprovado', 'text' => 'Ativo'],
        'inativo' => ['class' => 'status-rejeitado', 'text' => 'Inativo']
    ];
    
    $statusInfo = $statuses[$status] ?? $statuses['pendente'];
    return '<span class="status-badge ' . $statusInfo['class'] . '">' . $statusInfo['text'] . '</span>';
}

// Função para calcular taxas conforme legislação
function calcularTaxaLicenciamento($tipo_licenca, $zona = 'B') {
    $taxas_base = [
        'producao_grande_escala' => 900000000,
        'producao_media_escala' => 300000000,
        'producao_pequena_escala' => 150000000,
        'armazenagem_terminal' => 60000000,
        'armazenagem_central' => 10000000,
        'distribuicao' => 5000000,
        'retalho_posto' => 100000,
        'retalho_central' => 120000,
        'exportacao' => 500000
    ];
    
    $taxa = $taxas_base[$tipo_licenca] ?? 0;
    
    // Aplicar multiplicador da zona (se aplicável)
    $multiplicadores_zona = ['A' => 1.2, 'B' => 1.0, 'C' => 0.8];
    $taxa *= $multiplicadores_zona[$zona] ?? 1.0;
    
    return $taxa;
}

// Função para formatar valores monetários
function formatCurrency($value) {
    return number_format($value, 2, ',', ' ') . ' MT';
}

// Função para registrar atividades
function registrarAtividade($acao, $descricao, $modulo = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO atividades_sistema (usuario_id, acao, descricao, modulo, ip_address) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_SESSION['user_id'] ?? null,
            $acao,
            $descricao,
            $modulo,
            $_SERVER['REMOTE_ADDR']
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Erro ao registrar atividade: " . $e->getMessage());
        return false;
    }
}

// Função para obter dados de um cliente
function getClienteById($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erro ao obter cliente: " . $e->getMessage());
        return false;
    }
}

// Função para obter dados de um processo
function getProcessoById($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.nome_razao_social 
            FROM processos p 
            LEFT JOIN clientes c ON p.cliente_id = c.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erro ao obter processo: " . $e->getMessage());
        return false;
    }
}

// Função para mostrar mensagens de sucesso/erro
function showAlert($message, $type = 'info') {
    $class = 'alert-' . $type;
    $icon = '';
    
    switch($type) {
        case 'success':
            $icon = 'check-circle';
            break;
        case 'danger':
            $icon = 'exclamation-triangle';
            break;
        case 'warning':
            $icon = 'exclamation-circle';
            break;
        default:
            $icon = 'info-circle';
    }
    
    return "<div class='alert $class'><i class='fas fa-$icon'></i> $message</div>";
}
?>