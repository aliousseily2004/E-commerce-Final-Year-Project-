const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
const sections = document.querySelectorAll('.section');

sidebarLinks.forEach(function(link) {
    link.addEventListener('click', function(event) {
        event.preventDefault();

        // Remove active class and hide all sections
        sidebarLinks.forEach(function(l) {
            l.classList.remove('active');
        });
        sections.forEach(function(s) {
            s.style.display = 'none';
        });

        // Add active class to the clicked link and show the corresponding section
        link.classList.add('active');
        const sectionId = link.getAttribute('data-section') + '-section';
        document.getElementById(sectionId).style.display = 'block';
    });
});