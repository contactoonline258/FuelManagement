<?php
include '../../includes/config.php';
requireAuth();

// Checklist baseado no DM 176/2014 e Decreto 89/2019
$checklist_dm176 = [
    [
        'categoria' => 'Localização e Distâncias',
        'itens' => [
            ['artigo' => 'Art. 15', 'descricao' => 'Distância mínima de 15m de edificações vizinhas', 'obrigatorio' => true],
            ['artigo' => 'Art. 16', 'descricao' => 'Distância mínima de 25m de zonas residenciais', 'obrigatorio' => true],
            ['artigo' => 'Art. 17', 'descricao' => 'Distância mínima de 50m de escolas e hospitais', 'obrigatorio' => true],
            ['artigo' => 'Art. 18', 'descricao' => 'Distância mínima de 100m de postos concorrentes', 'obrigatorio' => true],
        ]
    ],
    [
        'categoria' => 'Instalações de Segurança',
        'itens' => [
            ['artigo' => 'Art. 22', 'descricao' => 'Sistema de drenagem e separador de águas óleo', 'obrigatorio' => true],
            ['artigo' => 'Art. 25', 'descricao' => 'Sistema de protecção contra incêndios', 'obrigatorio' => true],
            ['artigo' => 'Art. 28', 'descricao' => 'Sinalização de segurança visível', 'obrigatorio' => true],
            ['artigo' => 'Art. 30', 'descricao' => 'Iluminação de emergência', 'obrigatorio' => true],
        ]
    ]
];

$checklist_decreto89 = [
    [
        'categoria' => 'Qualidade dos Produtos',
        'itens' => [
            ['artigo' => 'Art. 45', 'descricao' => 'Teor de enxofre máximo 50ppm no gasóleo', 'obrigatorio' => true],
            ['artigo' => 'Art. 46', 'descricao' => 'Octanagem mínima 95 RON na gasolina', 'obrigatorio' => true],
            ['artigo' => 'Art. 48', 'descricao' => 'Certificação de qualidade obrigatória', 'obrigatorio' => true],
        ]
    ],
    [
        'categoria' => 'Obrigações Comerciais',
        'itens' => [
            ['artigo' => 'Art. 32', 'descricao' => 'Manutenção de reservas estratégicas', 'obrigatorio' => true],
            ['artigo' => 'Art. 55', 'descricao' => 'Afixação de preços visível ao público', 'obrigatorio' => true],
            ['artigo' => 'Art. 56', 'descricao' => 'Informação clara ao consumidor', 'obrigatorio' => true],
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist de Conformidade - PETROL CHECK</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <?php include '../../includes/sidebar.php'; ?>
        
        <div class="main-content">
            <header class="header">
                <h1><i class="fas fa-clipboard-check"></i> Checklist de Conformidade Legal</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                </div>
            </header>

            <div class="content-area">
                <!-- Legislação -->
                <div class="quick-actions" style="background: linear-gradient(135deg, var(--azul-petroleo) 0%, var(--azul-petroleo-escuro) 100%); color: white;">
                    <div>
                        <h2 style="color: white; margin-bottom: 10px;">Verificação de Conformidade Legal</h2>
                        <p style="color: rgba(255,255,255,0.9);">
                            Checklist completo baseado no Diploma Ministerial 176/2014 e Decreto 89/2019
                        </p>
                    </div>
                </div>

                <!-- DM 176/2014 -->
                <div class="card" style="margin-bottom: 30px;">
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda); background: var(--azul-petroleo); color: white;">
                        <h3 style="color: white;">
                            <i class="fas fa-file-contract"></i> Diploma Ministerial n.º 176/2014
                        </h3>
                        <p style="opacity: 0.9; margin: 0;">Regulamento de Construção, Exploração e Segurança dos Postos de Combustíveis</p>
                    </div>
                    
                    <?php foreach ($checklist_dm176 as $categoria): ?>
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda);">
                        <h4 style="color: var(--azul-petroleo); margin-bottom: 20px;">
                            <i class="fas fa-folder"></i> <?php echo $categoria['categoria']; ?>
                        </h4>
                        
                        <div class="grid grid-1">
                            <?php foreach ($categoria['itens'] as $item): ?>
                            <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--cinza-fundo); border-radius: 8px; margin-bottom: 10px;">
                                <div style="min-width: 80px;">
                                    <span class="badge badge-info"><?php echo $item['artigo']; ?></span>
                                </div>
                                <div style="flex: 1;">
                                    <strong style="color: var(--azul-petroleo);"><?php echo $item['descricao']; ?></strong>
                                </div>
                                <div style="min-width: 120px; text-align: center;">
                                    <?php if ($item['obrigatorio']): ?>
                                        <span class="badge badge-danger">Obrigatório</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Recomendado</span>
                                    <?php endif; ?>
                                </div>
                                <div style="min-width: 100px;">
                                    <select class="form-control" style="padding: 8px;">
                                        <option value="">Selecionar</option>
                                        <option value="conforme">Conforme</option>
                                        <option value="nao_conforme">Não Conforme</option>
                                        <option value="nao_aplica">Não se Aplica</option>
                                    </select>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Decreto 89/2019 -->
                <div class="card">
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda); background: #2C3E50; color: white;">
                        <h3 style="color: white;">
                            <i class="fas fa-oil-can"></i> Decreto n.º 89/2019
                        </h3>
                        <p style="opacity: 0.9; margin: 0;">Regulamento sobre os Produtos Petrolíferos</p>
                    </div>
                    
                    <?php foreach ($checklist_decreto89 as $categoria): ?>
                    <div style="padding: 25px; border-bottom: 1px solid var(--cinza-borda);">
                        <h4 style="color: #2C3E50; margin-bottom: 20px;">
                            <i class="fas fa-folder"></i> <?php echo $categoria['categoria']; ?>
                        </h4>
                        
                        <div class="grid grid-1">
                            <?php foreach ($categoria['itens'] as $item): ?>
                            <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--cinza-fundo); border-radius: 8px; margin-bottom: 10px;">
                                <div style="min-width: 80px;">
                                    <span class="badge badge-info"><?php echo $item['artigo']; ?></span>
                                </div>
                                <div style="flex: 1;">
                                    <strong style="color: #2C3E50;"><?php echo $item['descricao']; ?></strong>
                                </div>
                                <div style="min-width: 120px; text-align: center;">
                                    <?php if ($item['obrigatorio']): ?>
                                        <span class="badge badge-danger">Obrigatório</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Recomendado</span>
                                    <?php endif; ?>
                                </div>
                                <div style="min-width: 100px;">
                                    <select class="form-control" style="padding: 8px;">
                                        <option value="">Selecionar</option>
                                        <option value="conforme">Conforme</option>
                                        <option value="nao_conforme">Não Conforme</option>
                                        <option value="nao_aplica">Não se Aplica</option>
                                    </select>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Botão de Submissão -->
                <div style="text-align: center; margin-top: 30px;">
                    <button class="btn-cta" style="padding: 15px 30px; font-size: 1.1em;">
                        <i class="fas fa-paper-plane"></i> Submeter Verificação de Conformidade
                    </button>
                    <p style="color: var(--cinza-medio); margin-top: 15px;">
                        Esta verificação será arquivada como evidência de conformidade legal
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/app.js"></script>
</body>
</html>