<meta charset="UTF-8">
<meta name="viewport"
    content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', setting('site_title') ?: trans('panel.site_title'))</title>

<link rel="shortcut icon" href="{{ setting('site_favicon') ? asset('uploads/settings/' . setting('site_favicon')) : asset('assets/images/logo.svg') }}" type="image/x-icon">

{{-- Tailwind er Darkmode er sathe conflict kore tai bad dchi --}}
{{--
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" /> --}}

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
<link href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
<link
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"
    rel="stylesheet" />
{{--
<link href="https://unpkg.com/@coreui/coreui@3.2/dist/css/coreui.min.css" rel="stylesheet" /> --}}
{{-- cdn to local --}}
<link href="{{ asset('/assets/cdns/css/coreui.min.css') }}" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.0/css/perfect-scrollbar.min.css"
    rel="stylesheet" />
<link href="{{ asset('css/custom.css') }}" rel="stylesheet" />



<link
    href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;600;700&amp;display=swap"
    rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />


<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#137fec",
                    "primary-hover": "#0062cc",
                    "background-light": "#f6f6f8",
                    "background-dark": "#101622",
                    "text-main": "#0f172a",
                    "text-secondary": "#64748b",
                    "card-light": "#ffffff",
                    "card-dark": "#1a2632",
                    "border-light": "#e2e8f0",
                    "border-dark": "#334155",
                },
                fontFamily: {
                    "display": ["Lexend", "sans-serif"],
                    "body": ["Noto Sans", "sans-serif"]
                },
                borderRadius: {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
                },
            },
        },
    }
</script>
<style>
    /* body {
            font-family: 'Inter', sans-serif;
        } */

    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    /* Custom scrollbar for modern feel */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    /* ::-webkit-scrollbar-track {
            background: #111318;
        } */

    /* ::-webkit-scrollbar-thumb {
            background: #282e39;
            border-radius: 4px;
        } */

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #3d4655;
    }

    .required::after {
        content: " *";
        color: red;
    }
</style>