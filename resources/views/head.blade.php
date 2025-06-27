<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Icon Bootstrap --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/sass/app.scss'])

    {{-- Cropper JS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- SortableJS untuk Drag and Drop --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    {{-- TinyMCE untuk Teks Editor --}}
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>

    <script>
        tinymce.init({
            selector: 'textarea.editor', // pakai class "editor"
            plugins: 'lists link image code',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | removeformat',
            menubar: false,
            height: 300,
            license_key: 'gpl',
            branding: false,
            content_style: "body { font-family:Nunito,Helvetica; font-size:14px }"
        });
    </script>

    {{-- Scroll Smooth --}}
    <style>
        html {
            scroll-behavior: smooth;
        }

        /* Custom CSS untuk responsive layout */
        /* Sidebar styles */
        .sidebar {
            background-color: #fff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        /* Offcanvas customization */
        .offcanvas {
            background-color: #fff;
        }

        .offcanvas-header {
            background-color: #f8f9fa;
        }

        /* Responsive adjustments */
        @media (max-width: 575.98px) {

            /* Mobile styles */
            .content-bar-header .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .sidebar-profile .card-body {
                padding: 1rem;
            }

            .sidebar-menu {
                padding: 1rem;
            }

            .sidebar-profile .icon-profile {
                width: 20px !important;
                height: 20px !important;
            }

            .sidebar-profile .rounded-circle {
                width: 100px !important;
                height: 100px !important;
            }

            .sidebar-profile .sidebar-profile-photo {
                margin-bottom: 10px !important;
            }

            .content-bar-header .header-title-subtitle h5 {
                font-size: medium;
            }

            .content-bar-header .header-title-subtitle p {
                font-size: small;
            }

            .custom-carousel-height {
                height: 100px;
            }

            .hero-section {
                height: 120vh !important;
            }

            .hero-section img {
                max-width: 350px !important;
            }

            .contact-section img {
                width: 30px !important;
                height: 30px !important;
            }

            .contact-section .prose p {
                margin-bottom: 0px !important;
            }
        }

        @media (min-width: 576px) and (max-width: 767.98px) {

            /* Large mobile styles */
            .content-bar-header .container {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            .contact-section .prose p {
                margin-bottom: 0px !important;
            }

            .sidebar-profile img {
                width: 60px !important;
                height: 60px !important;
            }
        }

        @media (min-width: 768px) and (max-width: 1199.98px) {

            /* Tablet styles */
            .content-bar-header .container {
                padding-left: 2rem;
                padding-right: 2rem;
            }

            .custom-carousel-height {
                height: 300px;
            }

            .contact-section .prose p {
                margin-bottom: 0px !important;
            }

            .sidebar-profile img {
                width: 100px !important;
                height: 100px !important;
            }

            .hero-section .hero-text h2 {
                justify-content: center;
            }
        }

        @media (min-width: 1200px) {

            /* Desktop styles */
            .sidebar {
                width: 320px !important;
                min-width: 320px;
            }

            .custom-carousel-height {
                width: 100%;
                height: 400px;
                object-fit: cover;
            }

            .contact-section .prose p {
                margin-bottom: 0px !important;
            }
        }

        /* Hamburger button styling */
        .btn-outline-success {
            border-color: #0BAF6A;
            color: #0BAF6A;
        }

        .btn-outline-success:hover {
            background-color: #0BAF6A;
            border-color: #0BAF6A;
        }

        /* Menu item hover effects */
        .nav-link:hover {
            background-color: rgba(11, 175, 106, 0.1) !important;
            color: #0BAF6A !important;
        }

        /* Active menu item */
        .nav-link.text-success {
            background-color: #fff !important;
            color: #0BAF6A !important;
            font-weight: bold;
        }

        /* Dropdown menu styling */
        .dropdown-menu {
            border: 1px solid #dee2e6;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        /* Profile photo responsive */
        .sidebar-profile img {
            transition: all 0.3s ease;
        }

        /* Ensure offcanvas is above other content */
        .offcanvas {
            z-index: 1050;
        }

        /* Smooth transitions */
        .offcanvas {
            transition: transform 0.3s ease-in-out;
        }

        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar,
        .offcanvas-body::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track,
        .offcanvas-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .sidebar::-webkit-scrollbar-thumb,
        .offcanvas-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover,
        .offcanvas-body::-webkit-scrollbar-thumb:hover {
            background: #0BAF6A;
        }

        /* Custom scrollbar for content */
        .content-bar::-webkit-scrollbar,
        .offcanvas-body::-webkit-scrollbar {
            width: 6px;
        }

        .content-bar::-webkit-scrollbar-track,
        .offcanvas-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .content-bar::-webkit-scrollbar-thumb,
        .offcanvas-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .content-bar::-webkit-scrollbar-thumb:hover,
        .offcanvas-body::-webkit-scrollbar-thumb:hover {
            background: #0BAF6A;
        }
    </style>



</head>

</html>
