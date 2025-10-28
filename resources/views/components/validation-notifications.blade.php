@if (session('success'))
    <script>
        Toastify({
            text: `{{ session('success') }}`,
            duration: 5000,
            style: {
                background: "linear-gradient(90deg, rgba(121,237,91,1) 39%, rgba(66,222,62,1) 100%)",
            },
        }).showToast();
    </script>
@endif
@if (session('error'))
    <script>
        Toastify({
            text: `{{ session()->pull('error') }}`,
            duration: 10000,
            style: {
                background: "linear-gradient(90deg, rgba(250,43,43,1) 0%, rgba(252,133,69,1) 100%)",
            },
        }).showToast();
    </script>
@endif
@if ($errors->any())
    @php
        $errorUlInner = '';
        foreach ($errors->all() as $value) {
            $errorUlInner .= "<li>{$value}</li>";
        }
        $errorHtmlAcl = '<p><strong>Something went wrong</strong></p>';
        $errorHtmlAcl .= "<ul>{$errorUlInner}</ul>";
    @endphp
    <script>
        // Show toast on normal load
        const errorNode = document.createElement("div");
        errorNode.innerHTML = `{!! $errorHtmlAcl !!}`;
        const toastInstance = Toastify({
            node: errorNode,
            duration: 5000,
            style: {
                background: "linear-gradient(90deg, rgba(250,43,43,1) 0%, rgba(252,133,69,1) 100%)",
            },
        });

        // On normal page load, show toast
        window.addEventListener('load', function() {
            // If page NOT restored from back/forward cache, show toast
            if (performance.navigation.type !== performance.navigation.TYPE_BACK_FORWARD) {
                toastInstance.showToast();
            }
            // Otherwise, do NOT show toast (prevents duplicate on back/forward)
        });
    </script>
@endif

