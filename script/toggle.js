document.querySelector('.toggle').addEventListener('click', function() {
    document.querySelector('.navigation').classList.toggle('active');
    document.querySelector('.main').classList.toggle('active');
});

document.querySelectorAll('.navigation li').forEach(function(item) {
    item.addEventListener('mouseover', function() {
        document.querySelectorAll('.navigation li').forEach(function(item) {
            item.classList.remove('hovered');
        });
        this.classList.add('hovered');
    });
});