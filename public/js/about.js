
AOS.init({
    duration: 800,
    once: true
});

window.onscroll = function() {
    const backToTop = document.getElementById('backToTop');
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        backToTop.style.display = 'block';
    } else {
        backToTop.style.display = 'none';
    }
};

document.getElementById('backToTop').onclick = function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

function submitQuote() {
    const form = document.getElementById('quoteForm');
    if (form.checkValidity()) {
        alert('Terima kasih! Permintaan Anda telah dikirim.');
        form.reset();
        bootstrap.Modal.getInstance(document.getElementById('quoteModal')).hide();
    } else {
        form.reportValidity();
    }
} 