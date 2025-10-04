class FuelManagerApp {
    constructor() {
        this.init();
    }
    
    init() {
        this.loadDashboardStats();
        this.loadRecentActivities();
        this.setupEventListeners();
    }
    
    async loadDashboardStats() {
        try {
            const response = await this.apiCall('api/dashboard.php');
            this.updateStats(response);
        } catch (error) {
            this.showError('Erro ao carregar estatísticas');
        }
    }
    
    updateStats(data) {
        const elements = {
            'processos-ativos': data.processosAtivos,
            'vistorias-pendentes': data.vistoriasPendentes,
            'total-clientes': data.totalClientes,
            'receita-mensal': this.formatCurrency(data.receitaMensal)
        };
        
        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                this.animateValue(element, 0, value, 1000);
            }
        });
    }
    
    animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const value = Math.floor(progress * (end - start) + start);
            element.textContent = element.id === 'receita-mensal' ? 
                this.formatCurrency(value) : value;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
    
    formatCurrency(value) {
        return new Intl.NumberFormat('pt-MZ', {
            style: 'currency',
            currency: 'MZN'
        }).format(value);
    }
    
    async apiCall(url, options = {}) {
        const response = await fetch(url, options);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    }
    
    async loadRecentActivities() {
        try {
            const response = await this.apiCall('api/atividades.php');
            const atividades = await response.json();
            
            const container = document.getElementById('activities-list');
            if (atividades.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Nenhuma atividade recente</p>';
                return;
            }
            
            container.innerHTML = atividades.map(ativ => `
                <div class="activity-item">
                    <strong>${ativ.titulo}</strong>
                    <span>${ativ.descricao}</span>
                    <small>${new Date(ativ.data).toLocaleDateString('pt-PT')} às ${new Date(ativ.data).toLocaleTimeString('pt-PT', {hour: '2-digit', minute:'2-digit'})}</small>
                </div>
            `).join('');
        } catch (error) {
            console.error('Erro ao carregar atividades:', error);
            document.getElementById('activities-list').innerHTML = '<p style="text-align: center; color: #E74C3C;">Erro ao carregar atividades</p>';
        }
    }
    
    setupEventListeners() {
        // Event listeners para funcionalidades comuns
        document.addEventListener('click', this.handleGlobalClicks.bind(this));
    }
    
    handleGlobalClicks(event) {
        // Handler para clicks globais
        if (event.target.matches('[data-action="logout"]')) {
            this.logout();
        }
    }
    
    logout() {
        if (confirm('Tem certeza que deseja sair?')) {
            window.location.href = 'logout.php';
        }
    }
    
    showError(message) {
        this.showNotification(message, 'danger');
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} fade-in`;
        notification.innerHTML = `
            <i class="fas fa-${this.getNotificationIcon(type)}"></i>
            ${message}
            <button onclick="this.parentElement.remove()" style="margin-left: auto; background: none; border: none; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
    
    getNotificationIcon(type) {
        const icons = {
            'success': 'check-circle',
            'warning': 'exclamation-triangle',
            'danger': 'exclamation-circle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    // Funções de cálculo de taxas
    calcularTaxas(tipoLicenca) {
        const taxas = {
            'producao_grande_escala': 900000000,
            'producao_media_escala': 300000000,
            'producao_pequena_escala': 150000000,
            'armazenagem_terminal': 60000000,
            'armazenagem_central': 10000000,
            'distribuicao': 5000000,
            'retalho_posto': 100000,
            'retalho_central': 120000,
            'exportacao': 500000
        };
        
        return taxas[tipoLicenca] || 0;
    }
    
    // Validação de formulários
    validarFormProcesso(formData) {
        const errors = [];
        
        if (!formData.cliente_id) errors.push('Selecione um cliente');
        if (!formData.tipo_licenca) errors.push('Selecione o tipo de licença');
        if (!formData.data_inicio) errors.push('Data de início é obrigatória');
        
        return errors;
    }
    
    // Upload de documentos
    async uploadDocument(processoId, file, tipo) {
        const formData = new FormData();
        formData.append('documento', file);
        formData.append('processo_id', processoId);
        formData.append('tipo', tipo);
        
        try {
            const response = await fetch('api/upload_documento.php', {
                method: 'POST',
                body: formData
            });
            return await response.json();
        } catch (error) {
            console.error('Erro no upload:', error);
            return { success: false, error: 'Erro no upload' };
        }
    }
    
    // Filtros e busca
    filterTable(tableId, searchTerm) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            let found = false;
            
            for (let j = 0; j < row.cells.length; j++) {
                const cell = row.cells[j];
                if (cell.textContent.toLowerCase().includes(searchTerm.toLowerCase())) {
                    found = true;
                    break;
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    }
}

// Inicializar app quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new FuelManagerApp();
});

// Funções globais para compatibilidade
function verificarReservas() {
    window.location.href = 'modules/reservas/gerenciar.php';
}

function registarAmostra() {
    window.location.href = 'modules/qualidade/registar_amostra.php';
}

function calcularTaxas() {
    window.location.href = 'calculadora_taxas.php';
}

function gerarRelatorio() {
    window.location.href = 'modules/relatorios/gerar.php';
}

function agendarVistoria() {
    window.location.href = 'modules/vistorias/agendar.php';
}

function controloQualidade() {
    window.location.href = 'modules/qualidade/';
}