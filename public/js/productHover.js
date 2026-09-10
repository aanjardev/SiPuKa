

document.querySelectorAll('.Produk-Baru-Item').forEach(item => {
    item.addEventListener('mouseenter', () => {
      item.style.boxShadow = "0 6px 12px rgba(0,0,0,0.15)";
    });
    item.addEventListener('mouseleave', () => {
      item.style.boxShadow = "";
    });
  });
  