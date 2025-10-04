<?php
include '../../includes/config.php';
requireAuth();

$tipo = $_GET['tipo'] ?? '';
$subtipo = $_GET['subtipo'] ?? '';
$periodo = $_GET['periodo'] ?? '30';

// Calcular datas baseadas no período
$data_fim = date('Y-m-d');
switch ($periodo) {
    case '30':
        $data_inicio = date('Y-m-d', strtotime('-30 days'));
        break;
    case '90':
        $data_inicio = date('Y-m-d', strtotime('-90 days'));
        break;
    case '365':
        $data_inicio = date('Y-m-d', strtotime('-365 days'));
        break;
    default:
        $data_inicio = date('Y-m-d', strtotime('-30 days'));
}

// Headers para forçar download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="relatorio_' . $tipo . '_' . date('Y-m-d') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório - PETROL CHECK</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { background: #1A2A3A; color: white; padding: 20px; text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th { background: #D4AF37; color: #1A2A3A; padding: 10px; text-align: left; }
        .table td { padding: 8px; border: 1px solid #ddd; }
        .total { background: #f5f5f5; font-weight: bold; }
        .conforme { color: green; }
        .nao-conforme { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PETROL CHECK - Relatório do Sistema</h1>
        <p>Gerado em: <?php echo date('d/m/Y H:i'); ?> | Por: <?php echo $_SESSION['user_name']; ?></p>
    </div>

    <?php if ($tipo == 'processos'): ?>
        <!-- Relatório de Processos -->
        <h2>Relatório de Processos - Período: <?php echo date('d/m/Y', strtotime($data_inicio)) . ' a ' . date('d/m/Y', strtotime($data_fim)); ?></h2>
        
        <?php
        try {
            $stmt = $pdo->prepare("
                SELECT p.*, c.nome_razao_social 
                FROM processos p 
                LEFT JOIN clientes c ON p.cliente_id = c.id 
                WHERE p.created_at BETWEEN ? AND ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$data_inicio . ' 00:00:00', $data_fim . ' 23:59:59']);
            $processos = $stmt->fetchAll();
            
            // Estatísticas
            $stmt = $pdo->prepare("
                SELECT status, COUNT(*) as total 
                FROM processos 
                WHERE created_at BETWEEN ? AND ?
                GROUP BY status
            ");
            $stmt->execute([$data_inicio . ' 00:00:00', $data_fim . ' 23:59:59']);
            $stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            die("Erro ao gerar relatório: " . $e->getMessage());
        }
        ?>
        
        <h3>Resumo Estatístico</h3>
        <table class="table">
            <tr>
                <th>Status</th>
                <th>Quantidade</th>
                <th>Percentagem</th>
            </tr>
            <?php 
            $total_processos = array_sum($stats);
            foreach ($stats as $status => $quantidade): 
                $percentagem = $total_processos > 0 ? ($quantidade / $total_processos) * 100 : 0;
            ?>
            <tr>
                <td><?php echo ucfirst($status); ?></td>
                <td><?php echo $quantidade; ?></td>
                <td><?php echo number_format($percentagem, 1); ?>%</td>
            </tr>
            <?php endforeach; ?>
            <tr class="total">
                <td><strong>Total</strong></td>
                <td><strong><?php echo $total_processos; ?></strong></td>
                <td><strong>100%</strong></td>
            </tr>
        </table>
        
        <h3>Lista de Processos</h3>
        <table class="table">
            <tr>
                <th>Nº Processo</th>
                <th>Cliente</th>
                <th>Tipo Licença</th>
                <th>Data Início</th>
                <th>Status</th>
                <th>Responsável</th>
            </tr>
            <?php foreach ($processos as $processo): ?>
            <tr>
                <td><?php echo htmlspecialchars($processo['numero_processo']); ?></td>
                <td><?php echo htmlspecialchars($processo['nome_razao_social']); ?></td>
                <td><?php echo ucfirst(str_replace('_', ' ', $processo['tipo_licenca'])); ?></td>
                <td><?php echo date('d/m/Y', strtotime($processo['data_inicio'])); ?></td>
                <td><?php echo ucfirst($processo['status']); ?></td>
                <td><?php echo htmlspecialchars($processo['responsavel_tecnico']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

    <?php elseif ($tipo == 'conformidade'): ?>
        <!-- Relatório de Conformidade -->
        <h2>Relatório de Conformidade - <?php echo ucfirst($subtipo); ?></h2>
        
        <?php if ($subtipo == 'reservas' || $subtipo == 'completo'): ?>
            <h3>Reservas Estratégicas</h3>
            <?php
            try {
                $stmt = $pdo->prepare("
                    SELECT 
                        c.nome_razao_social,
                        r.produto,
                        r.quantidade_minima,
                        r.quantidade_atual,
                        CASE 
                            WHEN r.quantidade_atual >= r.quantidade_minima THEN 'conforme'
                            ELSE 'nao_conforme'
                        END as status,
                        ROUND((r.quantidade_atual / r.quantidade_minima) * 100, 1) as percentagem
                    FROM reservas_estrategicas r 
                    LEFT JOIN clientes c ON r.distribuidora_id = c.id 
                    ORDER BY c.nome_razao_social, r.produto
                ");
                $stmt->execute();
                $reservas = $stmt->fetchAll();
            } catch (PDOException $e) {
                die("Erro ao gerar relatório: " . $e->getMessage());
            }
            ?>
            
            <table class="table">
                <tr>
                    <th>Distribuidora</th>
                    <th>Produto</th>
                    <th>Volume Mínimo</th>
                    <th>Volume Actual</th>
                    <th>Percentagem</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reserva['nome_razao_social']); ?></td>
                    <td><?php echo strtoupper($reserva['produto']); ?></td>
                    <td><?php echo number_format($reserva['quantidade_minima'], 2); ?> m³</td>
                    <td><?php echo number_format($reserva['quantidade_atual'], 2); ?> m³</td>
                    <td><?php echo $reserva['percentagem']; ?>%</td>
                    <td class="<?php echo $reserva['status'] == 'conforme' ? 'conforme' : 'nao-conforme'; ?>">
                        <?php echo ucfirst($reserva['status']); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        
        <?php if ($subtipo == 'qualidade' || $subtipo == 'completo'): ?>
            <h3>Controlo de Qualidade</h3>
            <?php
            try {
                $stmt = $pdo->prepare("
                    SELECT 
                        cq.*, 
                        p.numero_processo,
                        c.nome_razao_social,
                        COUNT(*) as total,
                        COUNT(CASE WHEN resultado = 'conforme' THEN 1 END) as conformes,
                        COUNT(CASE WHEN resultado = 'nao_conforme' THEN 1 END) as nao_conformes
                    FROM controlo_qualidade cq 
                    LEFT JOIN processos p ON cq.processo_id = p.id 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    WHERE cq.data_amostragem BETWEEN ? AND ?
                    GROUP BY cq.produto_tipo
                ");
                $stmt->execute([$data_inicio, $data_fim]);
                $qualidade = $stmt->fetchAll();
            } catch (PDOException $e) {
                die("Erro ao gerar relatório: " . $e->getMessage());
            }
            ?>
            
            <table class="table">
                <tr>
                    <th>Produto</th>
                    <th>Total Amostras</th>
                    <th>Conformes</th>
                    <th>Não Conformes</th>
                    <th>Taxa Conformidade</th>
                </tr>
                <?php foreach ($qualidade as $item): 
                    $taxa_conformidade = $item['total'] > 0 ? ($item['conformes'] / $item['total']) * 100 : 0;
                ?>
                <tr>
                    <td><?php echo ucfirst(str_replace('_', ' ', $item['produto_tipo'])); ?></td>
                    <td><?php echo $item['total']; ?></td>
                    <td><?php echo $item['conformes']; ?></td>
                    <td><?php echo $item['nao_conformes']; ?></td>
                    <td class="<?php echo $taxa_conformidade >= 95 ? 'conforme' : 'nao-conforme'; ?>">
                        <?php echo number_format($taxa_conformidade, 1); ?>%
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

    <?php elseif ($tipo == 'financeiro'): ?>
        <!-- Relatório Financeiro -->
        <h2>Relatório Financeiro - <?php echo date('m/Y', strtotime($periodo)); ?></h2>
        
        <?php
        try {
            // Estatísticas de processos (simulação de receitas)
            $stmt = $pdo->prepare("
                SELECT 
                    tipo_licenca,
                    COUNT(*) as quantidade,
                    CASE 
                        WHEN tipo_licenca LIKE '%producao%' THEN 50000
                        WHEN tipo_licenca LIKE '%armazenagem%' THEN 30000
                        WHEN tipo_licenca LIKE '%distribuicao%' THEN 20000
                        WHEN tipo_licenca LIKE '%retalho%' THEN 10000
                        ELSE 15000
                    END as taxa_media
                FROM processos 
                WHERE YEAR(data_inicio) = ? AND MONTH(data_inicio) = ?
                GROUP BY tipo_licenca
            ");
            $ano_mes = explode('-', $periodo);
            $stmt->execute([$ano_mes[0], $ano_mes[1]]);
            $financeiro = $stmt->fetchAll();
            
            $receita_total = 0;
            foreach ($financeiro as $item) {
                $receita_total += $item['quantidade'] * $item['taxa_media'];
            }
        } catch (PDOException $e) {
            die("Erro ao gerar relatório: " . $e->getMessage());
        }
        ?>
        
        <h3>Resumo Financeiro</h3>
        <table class="table">
            <tr>
                <th>Tipo de Licença</th>
                <th>Quantidade</th>
                <th>Taxa Média (MT)</th>
                <th>Receita (MT)</th>
            </tr>
            <?php foreach ($financeiro as $item): 
                $receita_item = $item['quantidade'] * $item['taxa_media'];
            ?>
            <tr>
                <td><?php echo ucfirst(str_replace('_', ' ', $item['tipo_licenca'])); ?></td>
                <td><?php echo $item['quantidade']; ?></td>
                <td><?php echo number_format($item['taxa_media'], 0, ',', '.'); ?> MT</td>
                <td><?php echo number_format($receita_item, 0, ',', '.'); ?> MT</td>
            </tr>
            <?php endforeach; ?>
            <tr class="total">
                <td colspan="3"><strong>Receita Total</strong></td>
                <td><strong><?php echo number_format($receita_total, 0, ',', '.'); ?> MT</strong></td>
            </tr>
        </table>
        
        <h3>Projeções</h3>
        <table class="table">
            <tr>
                <th>Descrição</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>Receita Média Mensal</td>
                <td><?php echo number_format($receita_total, 0, ',', '.'); ?> MT</td>
            </tr>
            <tr>
                <td>Projeção Anual</td>
                <td><?php echo number_format($receita_total * 12, 0, ',', '.'); ?> MT</td>
            </tr>
            <tr>
                <td>Crescimento Estimado (15%)</td>
                <td><?php echo number_format($receita_total * 0.15, 0, ',', '.'); ?> MT</td>
            </tr>
        </table>

    <?php else: ?>
        <h2>Relatório não especificado</h2>
        <p>Selecione um tipo de relatório válido.</p>
    <?php endif; ?>

    <div style="margin-top: 50px; text-align: center; color: #666; font-size: 12px;">
        <p>Relatório gerado automaticamente pelo sistema PETROL CHECK</p>
        <p>Consultoria e Assessoria Especializada - Tel: +258 84 226 2987</p>
    </div>
</body>
</html>