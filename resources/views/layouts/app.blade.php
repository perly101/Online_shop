<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Absolute Essential Trading')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @yield('content')
    
    <script>
        // Convert ISO timestamps in data-ts into local time display
        if (!window.formatLocalTimes) {
            window.formatLocalTimes = function() {
                document.querySelectorAll('.local-time[data-ts], .order-date[data-ts]').forEach(el => {
                    const ts = el.getAttribute('data-ts');
                    if (!ts) return;
                    const d = new Date(ts);
                    if (isNaN(d)) return;
                    const datePart = d.toLocaleDateString(undefined, { month: 'short', day: '2-digit', year: 'numeric' });
                    const timePart = d.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit', hour12: true });
                    el.textContent = `${datePart} ${timePart}`;
                });
            };
            document.addEventListener('DOMContentLoaded', window.formatLocalTimes);
        }
    </script>

    @stack('scripts')
</body>
</html>
