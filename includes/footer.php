
    </main><!-- End Main Content -->

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Success Overlay (Premium) -->
    <div class="success-overlay" id="successOverlay">
        <div class="success-content">
            <div class="success-checkmark-premium">✓</div>
            <h2 class="success-title" style="font-size:1.8rem;">Success!</h2>
            <p class="success-message" style="font-size:1.1rem;">Action completed successfully</p>
            <button class="btn btn-primary btn-lg btn-glow-premium" onclick="PremiumEngine.hideSuccess()" style="margin-top:20px;">Continue</button>
        </div>
    </div>

    <!-- Footer Scripts -->
    <script src="/assets/js/main.js?v=2.0.0"></script>
    <script src="/assets/js/premium.js?v=2.0.0"></script>
    <script src="/assets/js/mobile.js?v=1.0.0"></script>
    
    <!-- Service Worker Registration -->
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(reg) {
                    console.log('[SW] Registered:', reg.scope);
                })
                .catch(function(err) {
                    console.log('[SW] Registration failed:', err);
                });
        });
    }
    </script>
    
    <!-- Lazy Load Ad Scripts -->
    <script>
    window.addEventListener('load', function() {
        var adScript1 = document.createElement('script');
        adScript1.src = 'https://intermediatenormalconfederate.com/63/ec/26/63ec26f8e4bdecfd5ae777f3a96fa119.js';
        adScript1.async = true;
        document.body.appendChild(adScript1);

        var adScript2 = document.createElement('script');
        adScript2.src = 'https://intermediatenormalconfederate.com/4f/ce/2d/4fce2d5c0aff67487512169e5ed4ba87.js';
        adScript2.async = true;
        document.body.appendChild(adScript2);
    });
    </script>
</body>
</html>
