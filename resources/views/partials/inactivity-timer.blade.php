<!-- Script Global de Detección de Inactividad (5 Minutos) -->
<script>
    (function () {
        // 5 minutos en milisegundos (5 * 60 * 1000 = 300,000 ms)
        const INACTIVITY_TIMEOUT_MS = 5 * 60 * 1000;
        const STORAGE_KEY = 'bbs_last_user_activity';
        const LOGOUT_URL = "{{ route('logout') }}?reason=inactivity";

        let timeoutId = null;
        let isLoggingOut = false;

        // Registrar la actividad actual
        function recordActivity() {
            if (isLoggingOut) return;
            const now = Date.now();
            try {
                localStorage.setItem(STORAGE_KEY, now.toString());
            } catch (e) {}
            resetInactivityTimer();
        }

        // Ejecutar el cierre de sesión por inactividad
        function triggerInactivityLogout() {
            if (isLoggingOut) return;
            isLoggingOut = true;

            // Si SweetAlert2 está disponible, mostrar aviso antes de redirigir
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sesión finalizada por inactividad',
                    text: 'Has superado los 5 minutos de inactividad. Cerrando sesión...',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    background: '#0c142b',
                    color: '#ffffff',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    window.location.href = LOGOUT_URL;
                });

                // Fallback por si la promesa tarda
                setTimeout(() => {
                    window.location.href = LOGOUT_URL;
                }, 2200);
            } else {
                window.location.href = LOGOUT_URL;
            }
        }

        // Reiniciar el temporizador local
        function resetInactivityTimer() {
            if (timeoutId) {
                clearTimeout(timeoutId);
            }

            // Calcular cuánto tiempo resta con base en la última actividad global registrada (multitab)
            let lastActivity = Date.now();
            try {
                const stored = localStorage.getItem(STORAGE_KEY);
                if (stored) {
                    lastActivity = parseInt(stored, 10);
                }
            } catch (e) {}

            const elapsed = Date.now() - lastActivity;
            const remaining = INACTIVITY_TIMEOUT_MS - elapsed;

            if (remaining <= 0) {
                triggerInactivityLogout();
                return;
            }

            timeoutId = setTimeout(function () {
                // Doble verificación del tiempo real transcurrido
                let updatedLast = lastActivity;
                try {
                    const st = localStorage.getItem(STORAGE_KEY);
                    if (st) updatedLast = parseInt(st, 10);
                } catch (e) {}

                if (Date.now() - updatedLast >= INACTIVITY_TIMEOUT_MS) {
                    triggerInactivityLogout();
                } else {
                    resetInactivityTimer();
                }
            }, remaining);
        }

        // Throttle para no saturar al mover el cursor rápidamente
        let lastThrottle = 0;
        function handleUserInteraction() {
            const now = Date.now();
            if (now - lastThrottle > 1000) { // Máximo una actualización por segundo
                lastThrottle = now;
                recordActivity();
            }
        }

        // Eventos a monitorear: ratón, teclado, scroll, toques táctiles y clics
        const activityEvents = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'];
        activityEvents.forEach(function (eventName) {
            window.addEventListener(eventName, handleUserInteraction, { passive: true });
        });

        // Sincronizar entre múltiples pestañas del mismo navegador
        window.addEventListener('storage', function (e) {
            if (e.key === STORAGE_KEY) {
                resetInactivityTimer();
            }
        });

        // Inicializar actividad al cargar la página
        recordActivity();
    })();
</script>
