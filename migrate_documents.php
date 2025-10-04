<?php
include 'includes/config.php';

try {
    $stmt = $pdo->query("SELECT id, nome_arquivo FROM documentos");
    $documentos = $stmt->fetchAll();
    
    foreach ($documentos as $doc) {
        $extensao = 'pdf'; // ou detectar baseado no tipo
        $nome_arquivo_unico = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $doc['nome_arquivo']) . '.' . $extensao;
        $novo_caminho = 'uploads/documentos/' . $nome_arquivo_unico;
        
        $update_stmt = $pdo->prepare("UPDATE documentos SET caminho_arquivo = ? WHERE id = ?");
        $update_stmt->execute([$novo_caminho, $doc['id']]);
    }
    
    echo "Documentos migrados com sucesso!";
} catch (PDOException $e) {
    echo "Erro na migração: " . $e->getMessage();
}
?>