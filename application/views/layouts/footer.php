
			</div>
		</main>
	</div>

	<!-- Footer -->
	<script>
		// Sidebar Toggle Functionality
		document.addEventListener('DOMContentLoaded', function() {
			const sidebar = document.getElementById('sidebar');
			const mainWrapper = document.getElementById('mainWrapper');
			const sidebarToggle = document.getElementById('sidebarToggle');
			const sidebarClose = document.getElementById('sidebarClose');
			const sidebarOverlay = document.getElementById('sidebarOverlay');

			// Cargar estado guardado del sidebar
			const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
			if (sidebarCollapsed && window.innerWidth >= 992) {
				sidebar.classList.add('collapsed');
				mainWrapper.classList.add('expanded');
			}

			// Toggle sidebar en desktop (contraer/expandir)
			sidebarToggle.addEventListener('click', function() {
				if (window.innerWidth >= 992) {
					sidebar.classList.toggle('collapsed');
					mainWrapper.classList.toggle('expanded');
					localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
				} else {
					sidebar.classList.toggle('show');
					sidebarOverlay.classList.toggle('show');
				}
			});

			// Cerrar sidebar en móvil
			if (sidebarClose) {
				sidebarClose.addEventListener('click', function() {
					sidebar.classList.remove('show');
					sidebarOverlay.classList.remove('show');
				});
			}

			// Cerrar sidebar al hacer clic en overlay
			sidebarOverlay.addEventListener('click', function() {
				sidebar.classList.remove('show');
				sidebarOverlay.classList.remove('show');
			});

			// Ajustar en resize
			window.addEventListener('resize', function() {
				if (window.innerWidth >= 992) {
					sidebar.classList.remove('show');
					sidebarOverlay.classList.remove('show');
				}
			});
		});
	</script>
</body>
</html>