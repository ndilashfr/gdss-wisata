// Function untuk handling delete confirmation
// Dipanggil di tombol hapus: onclick="return confirm(...)"
// Script ini bisa diperluas jika ingin pakai SweetAlert

document.addEventListener("DOMContentLoaded", function() {
    console.log("GDSS Wisata Ready!");

    // Contoh: Highlight baris tabel saat dihover
    const rows = document.querySelectorAll("table tbody tr");
    rows.forEach(row => {
        row.addEventListener("mouseenter", () => {
            row.classList.add("table-active");
        });
        row.addEventListener("mouseleave", () => {
            row.classList.remove("table-active");
        });
    });
});