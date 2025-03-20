<!-- Sidebar -->
<ul class="main-sidebar navbar-nav sidebar sidebar-dark accordion shadow" style="z-index: 1;" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center mb-4" href="">
        <div class="sidebar-brand-icon">
            <img class="img-fluid p-3 mt-5" src="<?= base_url('assets/images/msu.png'); ?>" alt="MSU Logo" style="width: 6rem;">
        </div>
    </a>
    <hr class="sidebar-divider mt-3">
    <?php
    // Get current URL
    $current_url = $this->uri->uri_string();

    // Get admin's role IDs from session
    $admin_data = $this->session->userdata('admin');
    $admin_role_ids = isset($admin_data['role_ids']) ? $admin_data['role_ids'] : []; // Ensure it's an array

    // Helper function to check permissions
    function has_permission($user_role_ids, $required_role_id) {
        return in_array($required_role_id, $user_role_ids);
    }

    $menuStructure = [
        'Dashboard' => [
            'icon' => 'fas fa-tachometer-alt',
            'items' => [
                ['title' => 'Home', 'url' => 'admin/dashboard', 'icon' => 'fas fa-home'],
            ]
        ],
        'Admin Management' => [
            'icon' => 'fas fa-user-shield',
            'required_role' => 1,
            'items' => [
                ['title' => 'Accounts', 'url' => 'admin/admin_management/accounts', 'icon' => 'fas fa-fw fa-users'],
            ]
        ],
        'Logs' => [
            'icon' => 'fas fa-clipboard-list',
            'required_role' => 2,
            'items' => [
                ['title' => 'Entry Logs', 'url' => 'admin/logs/entry', 'icon' => 'fas fa-door-open'],
                ['title' => 'Exit Logs', 'url' => 'admin/logs/exit', 'icon' => 'fas fa-door-closed'],
            ]
        ],
        'User Management' => [
            'icon' => 'fas fa-users',
            'required_role' => 3,
            'items' => [
                ['title' => 'Students', 'url' => 'admin/user_management/students', 'icon' => 'fas fa-user-graduate'],
                ['title' => 'Faculty', 'url' => 'admin/user_management/faculty', 'icon' => 'fas fa-chalkboard-teacher'],
                ['title' => 'Staff', 'url' => 'admin/user_management/staff', 'icon' => 'fas fa-user-tie'],
                ['title' => 'Residents', 'url' => 'admin/user_management/residents', 'icon' => 'fas fa-home'],
                ['title' => 'Guests', 'url' => 'admin/user_management/guests', 'icon' => 'fas fa-user-tag'],                
            ]
        ],
        'Visit Management' => [
            'icon' => 'fas fa-users',
            'required_role' => 4,
            'items' => [
                ['title' => 'Pre-Registered', 'url' => 'admin/visit_management/visitors_pending', 'icon' => 'fas fa-fw fa-hourglass-half'],
                ['title' => 'Registered Visitors', 'url' => 'admin/visit_management/visitors_active', 'icon' => 'fas fa-fw fa-id-badge'],
                // ['title' => 'Visitors Cards', 'url' => 'admin/visit_management/visitors_card', 'icon' => 'fas fa-fw fa-id-badge'],
                ['title' => 'Completed', 'url' => 'admin/visit_management/visitors_concluded', 'icon' => 'fas fa-fw fa-check'],
                ['title' => 'Declined', 'url' => 'admin/visit_management/visitors_declined', 'icon' => 'fas fa-fw fa-exclamation-circle'],
            ]
        ],
        'Security' => [
            'icon' => 'fas fa-shield-alt',
            'required_role' => 5,
            'items' => [
                // ['title' => 'Login Activity', 'url' => 'admin/security/security_logs', 'icon' => 'fas fa-sign-in-alt'],
                // ['title' => 'Failed Logins', 'url' => 'admin/security/security_logs', 'icon' => 'fas fa-user-times'],
                // ['title' => 'Password Changes', 'url' => 'admin/security/security_logs', 'icon' => 'fas fa-key'],
                // ['title' => 'Critical Actions', 'url' => 'admin/security/security_logs', 'icon' => 'fas fa-exclamation-triangle'],
                ['title' => 'Security Logs', 'url' => 'admin/security/security_logs', 'icon' => 'fas fa-clipboard-list'],
                ['title' => 'ID Logs', 'url' => 'admin/security/id_logs', 'icon' => 'fas fa-id-card'],
            ]
        ],
        'Reports' => [
            'icon' => 'fas fa-chart-bar',
            'required_role' => 6,
            'items' => [
                ['title' => 'Logs', 'url' => 'admin/reports/logs', 'icon' => 'fas fa-chart-line'],
            ]
        ],
        'Configurations' => [
            'icon' => 'fas fa-cog',
            'required_role' => 7,
            'items' => [
                ['title' => 'Locations', 'url' => 'admin/configurations/locations', 'icon' => 'fas fa-map-marker'],
                ['title' => 'Devices', 'url' => 'admin/configurations/devices', 'icon' => 'fas fa-laptop'],
                ['title' => 'Colleges', 'url' => 'admin/configurations/colleges', 'icon' => 'fas fa-university'],
                ['title' => 'Departments', 'url' => 'admin/configurations/departments', 'icon' => 'fas fa-building'],
                ['title' => 'Programs', 'url' => 'admin/configurations/programs', 'icon' => 'fas fa-graduation-cap'],
                ['title' => 'Offices', 'url' => 'admin/configurations/offices', 'icon' => 'fas fa-briefcase'],
            ]
        ]
    ];

    foreach ($menuStructure as $menuName => $menuData) :
        // Check if the current menu section should be displayed based on the admin's roles
        if (empty($menuData['required_role']) || has_permission($admin_role_ids, $menuData['required_role'])) :
            $menuId = str_replace(' ', '', $menuName);
            $isActiveMenu = false;
            foreach ($menuData['items'] as $item) {
                if ($item['url'] == $current_url) {
                    $isActiveMenu = true;
                    break;
                }
            }
    ?>
        <li class="nav-item <?= $isActiveMenu ? 'active' : '' ?>">
            <a class="nav-link <?= $isActiveMenu ? '' : 'collapsed' ?>" href="#" data-toggle="collapse" data-target="#<?= $menuId ?>"
                aria-expanded="<?= $isActiveMenu ? 'true' : 'false' ?>" aria-controls="<?= $menuId ?>">
                <i class="<?= $menuData['icon'] ?>"></i>
                <span class="menu-name"><?= $menuName ?></span>
            </a>
            <div id="<?= $menuId ?>" class="collapse <?= $isActiveMenu ? 'show' : '' ?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white collapse-inner rounded" style="z-index: 1031;">
                    <?php foreach ($menuData['items'] as $item) : 
                        $isActiveItem = ($item['url'] == $current_url);
                    ?>
                        <a class="collapse-item <?= $isActiveItem ? 'active' : '' ?>" href="<?= base_url($item['url']); ?>">
                            <i class="<?= $item['icon']; ?> mr-2"></i><?= $item['title']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </li>
    <?php
        endif;
    endforeach;
    ?>
    <hr class="sidebar-divider mt-3">                    
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->