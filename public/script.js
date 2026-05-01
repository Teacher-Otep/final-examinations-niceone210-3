function showSection(sectionID){
    const allSections = document.querySelectorAll('.content, .homecontent');
    allSections.forEach(function(section) {
        section.style.display = 'none';
    });
    const activeSection = document.getElementById(sectionID);
    if(activeSection){
        activeSection.style.display = 'block';
    }
    window.history.replaceState({}, document.title, window.location.pathname);
}

document.getElementById('logo').addEventListener('click', function() {
    const contentSections = document.querySelectorAll('.content');
    contentSections.forEach(function(section) {
        section.style.display = 'none';
    });
    document.getElementById('home').style.display = 'block';
    window.history.replaceState({}, document.title, window.location.pathname);
});

function clearForm() {
    var inputs = document.querySelectorAll('input[type="text"], input[type="number"]');
    inputs.forEach(function(input) { input.value = ''; });
}

document.getElementById('clrbtn').addEventListener('click', function() {
    clearForm();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
    }
});

window.onload = function() {
    if (typeof startSection !== 'undefined' && startSection !== 'home') {
        showSection(startSection);
    } else {
        document.getElementById('home').style.display = 'block';
    }
}