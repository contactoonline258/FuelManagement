<?php
include '../../includes/config.php';
requireAuth();

if (!isAdmin()) {
    $_SESSION['error_message'] = 'Sem permissão para excluir vistorias!';
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    // Verificar se existe a vistoria
    $stmt = $pdo->prepare("SELECT id FROM vistorias WHERE id = ?");
    $stmt->execute([$id]);
    $vistoria = $stmt->fetch();
    
    if ($vistoria) {
        // Excluir a vistoria
        $stmt = $pdo->prepare("DELETE FROM vistorias WHERE id = ?");
        $stmt->execute([$id]);
        
        registrarAtividade('VISTORIA_EXCLUIDA', 'Vistoria excluída: #' . $id, 'vistorias');
        
        $_SESSION['success_message'] = 'Vistoria excluída com sucesso!';
    } else {
        $_SESSION['error_message'] = 'Vistoria não encontrada!';
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Erro ao excluir vistoria: ' . $e->getMessage();
}

header('Location: index.php');
exit;
?>