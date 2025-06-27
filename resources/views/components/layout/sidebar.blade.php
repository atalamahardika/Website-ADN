<!-- Desktop Sidebar (≥1200px) -->
<div class="sidebar d-none d-xl-flex flex-column position-sticky top-0 h-100 overflow-auto" 
     style="width: 320px; border-right: 1px solid #0BAF6A; min-height: 100vh;">
    <div class="container-fluid p-0">
        <x-sidebar.header />
        <x-sidebar.profile :user="Auth::user()"/>
        <x-sidebar.menu :user="Auth::user()" />
    </div>
</div>

<!-- Mobile/Tablet Offcanvas Sidebar (<1200px) -->
<div class="offcanvas offcanvas-start d-xl-none" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel" style="width: 320px;">
    <div class="offcanvas-header" style="border-bottom: 1px solid #0BAF6A;">
        <h5 class="offcanvas-title" id="sidebarOffcanvasLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="container-fluid p-0">
            <x-sidebar.header />
            <x-sidebar.profile :user="Auth::user()"/>
            <x-sidebar.menu :user="Auth::user()" />
        </div>
    </div>
</div>
