<div class="sidebar w-80 sticky top-0 z-10 h-full overflow-y-auto flex-shrink-0" style="border-right: 1px solid #0BAF6A">
    <div class="container">
        <x-sidebar.header />
        <x-sidebar.profile :user="Auth::user()"/>
        <x-sidebar.menu :user="Auth::user()" />
    </div>
</div>
