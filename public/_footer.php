    </div> <!-- Fecha container principal aberto no _header.php -->

    <footer class="footer mt-5 py-3 bg-light border-top">
        <div class="container text-center">
            <p class="mb-1 text-muted">&copy; <?php echo date('Y'); ?> — Sistema de Orçamentos</p>
            <p class="small text-secondary">
                Desenvolvido por <strong>Sua Empresa</strong> • 
                <a href="mailto:contato@suaempresa.com" class="text-decoration-none">contato@suaempresa.com</a>
            </p>
        </div>
    </footer>

    <!-- Scripts principais -->
    <script src="/assets/js/jquery-3.6.0.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/chart.min.js"></script>

    <!-- Scripts globais do sistema -->
    <script src="/assets/js/app.js"></script>

    <!-- Script de inicialização global -->
    <script>
      $(document).ready(function () {
        // Ativa tooltips Bootstrap
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Ativa popovers Bootstrap
        $('[data-bs-toggle="popover"]').popover();

        // Feedback visual em botões e formulários
        $('.btn').on('click', function () {
          $(this).blur();
        });

        // Scroll automático para o topo após salvar ou erro
        if (window.location.hash === '#salvo' || window.location.hash === '#erro') {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      });
    </script>

  </body>
</html>
