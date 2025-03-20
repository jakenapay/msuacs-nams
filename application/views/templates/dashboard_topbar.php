    
<div id="content-wrapper" class="d-flex flex-column">

<!-- Main Content -->
<div id="content">
<div id="modal-profile" class="iziModal"></div>

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
    <i class="fa fa-bars"></i>
    </button>
    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600"><?= $account['username']; ?></span>
                <img class="img-profile rounded-circle" src="<?= base_url($account['image']); ?>">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" id="adminProfile" aria-labelledby="userDropdown">
                <a class="dropdown-item" class="profile-btn border-0" id="profile-btn" data-id="<?= $this->session->userdata('admin')['id']; ?>">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Profile</a>

                <a class="dropdown-item" href="<?= base_url('admin/logout');?>" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Logout</a>
            </div>
        </li>
    </ul>
</nav>

<script>
$(document).ready(function() {
    //ADMIN PROFILE IN THE TOPBAR
    $('#profile-btn').click(function() {
        var id = $(this).data('id');
           //Edit Modal Configurations
        $("#modal-profile").iziModal({
            title: 'Admin Profile',
            icon: "fas fa-fw fa-user-shield",
            subtitle: 'View Admin Information',
            transitionIn: 'fadeInUp',
            transitionOut: 'fadeOutDown',
            width: 700,
            headerColor: "linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(94,4,4,1) 0%, rgba(94,4,4,0.6979166666666667) 96%)",
            fullscreen: true,
            onClosed: function() {
                $("izimodal").iziModal("destroy");
                $("izimodal").remove();
            }
        });
        $.ajax({
            url: site_url + 'admin/admin_management/accounts/view/' + id,
            type: 'GET',
            success: function(response) {
                $("#modal-profile").iziModal('setContent', response);
                $("#modal-profile").iziModal('open');
                // Initialize your script here
            },
            error: function() {
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to load the modal content.'
                });
            }
        });
    });
    
});   
</script>
