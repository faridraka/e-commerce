<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lokalku')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}}::-webkit-scrollbar{display:none;}</style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">tailwind.config={darkMode:"class",theme:{extend:{"colors":{"inverse-primary":"#adc6ff","surface-container-lowest":"#ffffff","surface-card":"#FFFFFF","on-secondary-fixed-variant":"#930010","secondary-container":"#dc3132","surface":"#fcf9f8","on-surface-variant":"#424753","tertiary-fixed":"#bef455","secondary":"#b8121d","on-tertiary":"#ffffff","outline":"#727785","primary-fixed-dim":"#adc6ff","surface-dim":"#dcd9d9","surface-container-highest":"#e5e2e1","inverse-surface":"#313030","on-tertiary-fixed-variant":"#364e00","inverse-on-surface":"#f3f0ef","on-tertiary-container":"#c2f959","surface-tint":"#005bc0","background":"#fcf9f8","on-primary":"#ffffff","on-error":"#ffffff","error-container":"#ffdad6","red-graphic":"#E53238","error":"#ba1a1a","canvas-background":"#F7F7F7","secondary-fixed-dim":"#ffb3ad","success-text":"#4D7A00","on-secondary-fixed":"#410003","primary":"#004da4","surface-container-low":"#f6f3f2","on-background":"#1c1b1b","surface-container-high":"#eae7e7","accent-yellow":"#F5AF02","tertiary":"#3d5800","border-input":"#8A8A8A","primary-fixed":"#d8e2ff","border-default":"#E5E5E5","ink-muted":"#707070","secondary-fixed":"#ffdad6","blue-subtle":"#E6F0FB","on-primary-fixed":"#001a41","on-tertiary-fixed":"#131f00","on-primary-fixed-variant":"#004493","primary-container":"#0064d2","tertiary-fixed-dim":"#a3d73a","tertiary-container":"#507200","surface-bright":"#fcf9f8","on-secondary":"#ffffff","outline-variant":"#c2c6d5","surface-container":"#f0eded","surface-variant":"#e5e2e1","on-secondary-container":"#fffbff","on-surface":"#1c1b1b","blue-hover":"#004FB3","on-accent-yellow":"#191919","on-error-container":"#93000a","on-primary-container":"#dfe7ff"},"borderRadius":{"DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px"},"spacing":{"margin-desktop":"1rem","space-xl":"1.5rem","gutter":"1rem","space-md":"0.75rem","space-sm":"0.5rem","space-xs":"0.25rem","gutter-mobile":"0.75rem","space-lg":"1rem","margin":"1rem"},"fontFamily":{"strikethrough":["DM Sans"],"caption":["DM Sans"],"price-lg":["DM Sans"],"headline-sm":["DM Sans"],"body-md":["DM Sans"],"price-sm":["DM Sans"],"display":["DM Sans"],"display-mobile":["DM Sans"],"headline-lg":["DM Sans"],"headline-md":["DM Sans"],"code-mono":["JetBrains Mono"]},"fontSize":{"strikethrough":["14px",{"lineHeight":"20px","fontWeight":"400"}],"caption":["12px",{"lineHeight":"16px","fontWeight":"500"}],"price-lg":["24px",{"lineHeight":"32px","fontWeight":"700"}],"headline-sm":["18px",{"lineHeight":"24px","fontWeight":"500"}],"body-md":["14px",{"lineHeight":"20px","fontWeight":"400"}],"price-sm":["16px",{"lineHeight":"24px","fontWeight":"700"}],"display":["36px",{"lineHeight":"44px","letterSpacing":"-0.01em","fontWeight":"700"}],"display-mobile":["28px",{"lineHeight":"36px","letterSpacing":"-0.01em","fontWeight":"700"}],"headline-lg":["28px",{"lineHeight":"36px","letterSpacing":"-0.01em","fontWeight":"700"}],"headline-md":["22px",{"lineHeight":"28px","fontWeight":"700"}],"code-mono":["14px",{"lineHeight":"20px","fontWeight":"500"}]}}}};</script>
</head>
<body class="bg-canvas-background text-on-surface min-h-screen flex flex-col justify-between selection:bg-primary-fixed selection:text-on-primary-fixed">
    @yield('header')

    @yield('content')

    <footer class="w-full bg-surface-container-lowest border-t border-border-default py-space-lg">
        <div class="max-w-7xl mx-auto px-margin-desktop flex flex-col sm:flex-row items-center justify-between gap-space-md font-caption text-caption text-ink-muted">
            <div class="flex items-center gap-space-xs">
                <span class="material-symbols-outlined text-tertiary-container text-[16px]">verified_user</span>
                <span class="font-medium text-on-surface">Transaksi Aman &amp; Terenkripsi 256-bit</span>
            </div>
            <div class="flex items-center gap-space-lg">
                <span>© 2025 Lokalku UMKM Digital. Seluruh hak cipta dilindungi.</span>
                <a class="hover:text-primary transition-colors" href="#">Bantuan</a>
                <a class="hover:text-primary transition-colors" href="#">Privasi</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
