<?php
// login.php
include 'includes/config.php';

// Se já estiver logado, vai para dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'ativo'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome_completo'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            
            // Registrar atividade
            registrarAtividade('LOGIN', 'Utilizador fez login no sistema');
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Credenciais inválidas!";
        }
    } catch (PDOException $e) {
        $error = "Erro no sistema: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PETROL CHECK</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-box">
            <div style="text-align: center; margin-bottom: 30px;">
                <i class="fas fa-gas-pump" style="font-size: 3em; color: var(--dourado); margin-bottom: 15px;"></i>
                <h1 style="color: var(--azul-petroleo); margin-bottom: 5px;">PETROL CHECK</h1>
                <p style="color: #666; margin-bottom: 20px;">Consultoria e Assessoria</p>
                <a href="welcome.php" style="color: var(--dourado); text-decoration: none; font-size: 0.9em;">
                    <i class="fas fa-arrow-left"></i> Voltar à página inicial
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Utilizador:</label>
                    <input type="text" name="username" class="form-control" required placeholder="Digite o seu utilizador">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password:</label>
                    <input type="password" name="password" class="form-control" required placeholder="Digite a sua password">
                </div>
                
                <button type="submit" class="btn-cta" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i> Entrar no Sistema
                </button>
            </form>
            
            <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--cinza-borda); text-align: center;">
                <p style="color: #666; margin-bottom: 15px;">Primeiro acesso?</p>
                <button onclick="location.href='modules/consultoria/solicitar.php'" class="btn-secondary" style="width: 100%;">
                    <i class="fas fa-handshake"></i> Solicite uma Consultoria
                </button>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: var(--cinza-fundo); border-radius: 5px; font-size: 0.8em; color: var(--cinza-medio);">
                <strong>Credenciais de Demonstração:</strong><br>
                Utilizador: <strong>admin</strong> | Password: <strong>password</strong><br>
                Utilizador: <strong>gestor</strong> | Password: <strong>password</strong><br>
                Utilizador: <strong>tecnico</strong> | Password: <strong>password</strong>
            </div>
        </div>
    </div>
</body>
</html>