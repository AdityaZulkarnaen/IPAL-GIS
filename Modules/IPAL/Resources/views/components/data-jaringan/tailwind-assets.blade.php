@push('ipal-styles')
<script>
    window.tailwind = window.tailwind || {};
    window.tailwind.config = {
        corePlugins: {
            preflight: false,
        },
        theme: {
            extend: {
                colors: {
                    ipalBlue: '#2a83ea',
                    ipalText: '#18213d',
                    ipalMuted: '#6f7c95',
                },
                boxShadow: {
                    panel: '0 2px 10px rgba(15, 23, 42, 0.04)',
                },
            },
        },
    };
</script>
<script src="https://cdn.tailwindcss.com"></script>
@endpush
