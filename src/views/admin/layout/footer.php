<?php
/**
 * Admin Layout - Footer
 * Rodapé do painel administrativo
 */
?>
            </div><!-- /.content-area -->
        </main><!-- /.main-content -->
    </div><!-- /.admin-wrapper -->

    <script>
        // Toggle Sidebar (Mobile)
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Close sidebar when clicking outside (Mobile)
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(event.target) && event.target !== menuToggle) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Auto-hide flash messages
        setTimeout(() => {
            const flashMessages = document.querySelector('.flash-messages');
            if (flashMessages) {
                flashMessages.style.opacity = '0';
                flashMessages.style.transition = 'opacity 0.5s ease';
                setTimeout(() => flashMessages.remove(), 500);
            }
        }, 5000);

        // Confirm delete actions
        document.querySelectorAll('[data-confirm]').forEach(element => {
            element.addEventListener('click', function(e) {
                const message = this.getAttribute('data-confirm') || 'Tem certeza que deseja excluir?';
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>