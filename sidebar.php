<?php
// includes/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
$current_module = '';

// Detectar módulo atual
$path = $_SERVER['REQUEST_URI'];
if (strpos($path, 'modules/') !== false) {
    $parts = explode('modules/', $path);
    if (isset($parts[1])) {
        $module_parts = explode('/', $parts[1]);
        $current_module = $module_parts[0];
    }
}
?>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <h2><i class="fas fa-gas-pump"></i> PETROL CHECK</h2>
        <small>Consultoria e Assessoria</small>
    </div>
    <nav class="menu">
        <a href="../../dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="../../modules/clientes/" class="<?php echo $current_module == 'clientes' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Clientes
        </a>
        <a href="../../modules/processos/" class="<?php echo $current_module == 'processos' ? 'active' : ''; ?>">
            <i class="fas fa-file-contract"></i> Processos
        </a>
        <a href="../../modules/vistorias/" class="<?php echo $current_module == 'vistorias' ? 'active' : ''; ?>">
            <i class="fas fa-clipboard-check"></i> Vistorias
        </a>
        <a href="../../modules/qualidade/" class="<?php echo $current_module == 'qualidade' ? 'active' : ''; ?>">
            <i class="fas fa-flask"></i> Controlo de Qualidade
        </a>
        <a href="../../modules/reservas/" class="<?php echo $current_module == 'reservas' ? 'active' : ''; ?>">
            <i class="fas fa-database"></i> Reservas Estratégicas
        </a>
        <a href="../../modules/documentos/" class="<?php echo $current_module == 'documentos' ? 'active' : ''; ?>">
            <i class="fas fa-folder"></i> Documentos
        </a>
        <a href="../../modules/relatorios/" class="<?php echo $current_module == 'relatorios' ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i> Relatórios
        </a>
        <a href="../../modules/consultoria/" class="<?php echo $current_module == 'consultoria' ? 'active' : ''; ?>">
            <i class="fas fa-handshake"></i> Consultoria
        </a>
        
        <a href="../../modules/conformidade/" class="<?php echo $current_module == 'conformidade' ? 'active' : ''; ?>">
        <i class="fas fa-gavel"></i> Conformidade Legal
        </a>
        <a href="../../logout.php">
            <i class="fas fa-sign-out-alt"></i> Sair
        </a>
    </nav>
</div>