<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  
      <script>
  (() => {
    const root = document.documentElement;
    root.classList.remove('dark');
    localStorage.setItem('darkMode','false');
    localStorage.setItem('theme','light');
    localStorage.setItem('color-theme','light');
    new MutationObserver(() => {
      if (root.classList.contains('dark')) root.classList.remove('dark');
    }).observe(root, { attributes:true, attributeFilter:['class'] });
  })();
  </script>
      <!-- Recomendado para agentes de usuario -->
  <meta name="color-scheme" content="light">
  <meta name="supported-color-schemes" content="light">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/img/logo_instituto.png">
    <link rel="icon" type="image/png" href="/assets/img/logo_instituto.png">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Nucleo Icons -->
    <link href="/assets/css/nucleo-icons.css" rel="stylesheet">
    <link href="/assets/css/nucleo-svg.css" rel="stylesheet">

    <!-- Main Styling -->
    <link href="/assets/css/argon-dashboard-tailwind.css?v=1.0.1" rel="stylesheet">

    <!-- Flaticon UIcons (Solid Rounded) -->
<link href="https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css" rel="stylesheet">


    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="m-0 font-sans text-base antialiased font-normal leading-default bg-white text-slate-700">
    @inertia

    <!-- Popper JS -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script>
        window.canvaConfig = {
          devKey: 'AAGmc1iy5yQ',
          origin: 'https://app-aagmc1iy5yq.canva-apps.com'
        }
      </script>
      
    <!-- Y justo después, carga el SDK de Canva -->
    <script src="https://sdk.canva.com/v1/embed.js"></script>

</body>
</html>