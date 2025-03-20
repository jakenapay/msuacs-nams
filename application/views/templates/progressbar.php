<div class="custom-progress-bar">
    <div class="progress-step <?php echo $progress == 15 ? 'active' : ''; ?>"></div>
    <div class="progress-step <?php echo $progress == 30 ? 'active' : ''; ?>"></div>
    <div class="progress-step <?php echo $progress == 45 ? 'active' : ''; ?>"></div>
    <div class="progress-step <?php echo $progress == 60 ? 'active' : ''; ?>"></div>
    <div class="progress-step <?php echo $progress == 75 ? 'active' : ''; ?>"></div>
    <div class="progress-step <?php echo $progress == 90 ? 'active' : ''; ?>"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var steps = document.querySelectorAll('.progress-step');
    for (var i = 0; i < steps.length; i++) {
        if (steps[i].classList.contains('active') && i > 0) {
            steps[i-1].classList.add('before-active');
            break;
        }
    }
});
</script>