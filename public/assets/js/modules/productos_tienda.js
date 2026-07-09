
document.addEventListener('DOMContentLoaded', function () {
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterContent = document.getElementById('filterCollapsibleContent');
    const filterIcon = document.querySelector('.filter-icon');

    if (filterToggleBtn && filterContent) {
        filterToggleBtn.addEventListener('click', function () {
            if (filterContent.style.display === 'none' || filterContent.style.display === '') {
                filterContent.style.display = 'block';
                if (filterIcon) filterIcon.classList.remove('bi-chevron-down');
                if (filterIcon) filterIcon.classList.add('bi-chevron-up');
            } else {
                filterContent.style.display = 'none';
                if (filterIcon) filterIcon.classList.remove('bi-chevron-up');
                if (filterIcon) filterIcon.classList.add('bi-chevron-down');
            }
        });
    }
});
