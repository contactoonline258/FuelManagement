<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PETROL CHECK - Consultoria e Assessoria</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .welcome-container {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--azul-petroleo) 0%, var(--azul-petroleo-escuro) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .welcome-box {
            background: var(--branco);
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
        }

        .welcome-header {
            background: linear-gradient(135deg, var(--azul-petroleo) 0%, var(--azul-petroleo-escuro) 100%);
            color: var(--branco);
            padding: 40px;
            text-align: center;
        }

        .logo-welcome {
            font-size: 4em;
            color: var(--dourado);
            margin-bottom: 20px;
        }

        .welcome-content {
            padding: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .features-grid {
            display: grid;
            gap: 20px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
            background: var(--cinza-fundo);
            border-radius: 8px;
            border-left: 4px solid var(--dourado);
        }

        .feature-icon {
            color: var(--dourado);
            font-size: 1.5em;
            margin-top: 2px;
        }

        .login-side {
            background: var(--cinza-fundo);
            padding: 30px;
            border-radius: 10px;
        }

        .stats-welcome {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }

        .stat-welcome {
            text-align: center;
            padding: 20px;
            background: var(--branco);
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: var(--azul-petroleo);
            display: block;
        }

        @media (max-width: 768px) {
            .welcome-content {
                grid-template-columns: 1fr;
            }
            
            .stats-welcome {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-box">
            <div class="welcome-header">
                <div class="logo-welcome">
                    <i class="fas fa-gas-pump"></i>
                </div>
                <h1 style="color: var(--dourado); margin-bottom: 10px; font-size: 2.5em;">PETROL CHECK</h1>
                <p style="font-size: 1.2em; opacity: 0.9;">Consultoria e Assessoria Especializada</p>
                <p style="margin-top: 20px; opacity: 0.8;">Sistema de Gestão para o Setor de Combustíveis</p>
            </div>

            <div class="welcome-content">
                <div>
                    <h2 style="color: var(--azul-petroleo); margin-bottom: 20px;">
                        <i class="fas fa-star"></i> Porque Escolher o PETROL CHECK?
                    </h2>
                    
                    <div class="features-grid">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h3 style="color: var(--azul-petroleo); margin-bottom: 5px;">Conformidade Legal</h3>
                                <p style="color: var(--cinza-medio);">Garanta o cumprimento integral da legislação petrolífera moçambicana.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <h3 style="color: var(--azul-petroleo); margin-bottom: 5px;">Gestão de Processos</h3>
                                <p style="color: var(--cinza-medio);">Controle completo de licenciamentos, vistorias e documentação.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-flask"></i>
                            </div>
                            <div>
                                <h3 style="color: var(--azul-petroleo); margin-bottom: 5px;">Controlo de Qualidade</h3>
                                <p style="color: var(--cinza-medio);">Sistema avançado de monitorização da qualidade dos produtos.</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <div>
                                <h3 style="color: var(--azul-petroleo); margin-bottom: 5px;">Reservas Estratégicas</h3>
                                <p style="color: var(--cinza-medio);">Gestão automática das reservas obrigatórias conforme Decreto 89/2019.</p>
                            </div>
                        </div>
                    </div>

                    <div class="stats-welcome">
                        <div class="stat-welcome">
                            <span class="stat-number">100%</span>
                            <span style="color: var(--cinza-medio);">Conformidade Legal</span>
                        </div>
                        <div class="stat-welcome">
                            <span class="stat-number">24/7</span>
                            <span style="color: var(--cinza-medio);">Suporte Especializado</span>
                        </div>
                        <div class="stat-welcome">
                            <span class="stat-number">50+</span>
                            <span style="color: var(--cinza-medio);">Clientes Ativos</span>
                        </div>
                        <div class="stat-welcome">
                            <span class="stat-number">500+</span>
                            <span style="color: var(--cinza-medio);">Processos Geridos</span>
                        </div>
                    </div>
                </div>

                <div class="login-side">
                    <h2 style="color: var(--azul-petroleo); margin-bottom: 20px; text-align: center;">
                        <i class="fas fa-lock"></i> Acesso ao Sistema
                    </h2>
                    
                    <div style="text-align: center; margin-bottom: 30px;">
                        <p style="color: var(--cinza-medio); margin-bottom: 20px;">
                            Entre no sistema para gerir seus processos e aceder a todas as funcionalidades.
                        </p>
                        <button onclick="location.href='login.php'" class="btn-cta" style="width: 100%;">
                            <i class="fas fa-sign-in-alt"></i> Fazer Login
                        </button>
                    </div>

                    <div style="border-top: 1px solid var(--cinza-borda); padding-top: 20px;">
                        <h3 style="color: var(--azul-petroleo); margin-bottom: 15px; font-size: 1.1em;">
                            <i class="fas fa-handshake"></i> Novo Cliente?
                        </h3>
                        <p style="color: var(--cinza-medio); margin-bottom: 20px; font-size: 0.9em;">
                            Solicite uma consultoria e descubra como podemos ajudar sua empresa.
                        </p>
                        <button onclick="location.href='modules/consultoria/solicitar.php'" class="btn-secondary" style="width: 100%;">
                            <i class="fas fa-phone-alt"></i> Solicitar Consultoria
                        </button>
                    </div>

                    <div style="border-top: 1px solid var(--cinza-borda); padding-top: 20px; margin-top: 20px;">
                        <h3 style="color: var(--azul-petroleo); margin-bottom: 15px; font-size: 1.1em;">
                            <i class="fas fa-info-circle"></i> Contactos
                        </h3>
                        <div style="color: var(--cinza-medio); font-size: 0.9em;">
                            <p><i class="fas fa-phone"></i> +258 84 226 2987</p>
                            <p><i class="fas fa-envelope"></i> petrolcheckmz@gmail.com</p>
                            <p><i class="fas fa-map-marker-alt"></i> Quelimane, Moçambique</p>
                        </div>
                    </div>
                </div>
            </div>

            <div style="background: var(--azul-petroleo-escuro); color: var(--branco); padding: 20px; text-align: center;">
                <p style="margin: 0; opacity: 0.8;">
                    &copy; 2025 PETROL CHECK - Consultoria e Assessoria. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Animação simples para os números
        document.addEventListener('DOMContentLoaded', function() {
            const stats = document.querySelectorAll('.stat-number');
            stats.forEach(stat => {
                const finalValue = parseInt(stat.textContent);
                if (!isNaN(finalValue)) {
                    animateNumber(stat, 0, finalValue, 2000);
                }
            });
        });

        function animateNumber(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const value = Math.floor(progress * (end - start) + start);
                element.textContent = value + (element.textContent.includes('%') ? '%' : '+');
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }
    </script>
</body>
</html>