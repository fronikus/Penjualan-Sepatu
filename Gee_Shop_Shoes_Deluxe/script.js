document.addEventListener('DOMContentLoaded', () => {
    // === Seleksi Elemen DOM ===
    const navLinks = document.querySelector('.nav-links');
    const burger = document.querySelector('.burger');
    const categoryButtons = document.querySelectorAll('.category-btn');
    const productCards = document.querySelectorAll('.product-card');
    const searchLink = document.getElementById('searchLink');
    const cartCountElement = document.querySelector('.cart-count');
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn'); // Tombol "Tambah Keranjang" di index.php
    
    // === Navigasi Burger Menu (Mobile) ===
    if (burger && navLinks) { // Pastikan elemen ada sebelum menambahkan event listener
        burger.addEventListener('click', () => {
            navLinks.classList.toggle('nav-active');
            burger.classList.toggle('toggle');

            // Animasi link navigasi
            navLinks.querySelectorAll('li').forEach((link, index) => {
                if (navLinks.classList.contains('nav-active')) {
                    link.style.setProperty('--delay', `${index * 0.1 + 0.3}s`);
                    link.style.animation = `navLinkFade 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards var(--delay)`;
                } else {
                    link.style.animation = ''; // Reset animasi
                }
            });
        });
    }

    // Menutup menu burger saat link diklik
    if (navLinks) {
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (burger && burger.classList.contains('toggle')) { // Periksa juga apakah burger dalam keadaan "terbuka"
                    navLinks.classList.remove('nav-active');
                    burger.classList.remove('toggle');
                    navLinks.querySelectorAll('li').forEach(item => item.style.animation = ''); // Hapus animasi
                }
            });
        });
    }

    // === Filter Produk Berdasarkan Kategori ===
    if (categoryButtons.length > 0 && productCards.length > 0) {
        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Hapus kelas 'active' dari semua tombol, tambahkan ke tombol yang diklik
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                const selectedCategory = button.dataset.category;

                let cardsToHide = [];
                let cardsToShow = [];

                // Pisahkan kartu yang akan disembunyikan dan ditampilkan
                productCards.forEach(card => {
                    const productCategory = card.dataset.category;
                    if (selectedCategory === 'all' || productCategory === selectedCategory) {
                        cardsToShow.push(card);
                    } else {
                        cardsToHide.push(card);
                    }
                });

                // Animasi menyembunyikan kartu
                cardsToHide.forEach(card => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(30px)';
                    card.classList.remove('visible-card');
                    // Tunggu transisi selesai sebelum mengatur display ke 'none'
                    setTimeout(() => card.style.display = 'none', 500); 
                });

                // Animasi menampilkan kartu setelah yang lain disembunyikan
                setTimeout(() => {
                    let delay = 0;
                    cardsToShow.forEach(card => {
                        card.style.display = 'flex'; // Atau 'grid', tergantung layout Anda
                        // Atur ulang opacity dan transform untuk animasi masuk
                        card.style.opacity = '1'; 
                        card.style.transform = 'translateY(0)';
                        card.style.setProperty('--card-delay', `${delay}s`);
                        card.classList.add('visible-card');
                        delay += 0.08;
                    });
                }, 300); // Penundaan untuk memastikan kartu tersembunyi dulu

            });
        });

        // Klik tombol "Semua" secara otomatis saat halaman dimuat
        const allCategoryButton = document.querySelector('.category-btn[data-category="all"]');
        if (allCategoryButton) {
            allCategoryButton.click();
        }
    }

    // === Fitur Pencarian (Placeholder) ===
    if (searchLink) {
        searchLink.addEventListener('click', (event) => {
            event.preventDefault(); // Mencegah perilaku default link
            alert('Fitur pencarian canggih akan hadir segera!');
        });
    }

    // === Update Jumlah Item Keranjang (AJAX) ===
    function updateCartCount() {
        fetch('actions/get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                if (cartCountElement && data.success) { // Pastikan data.success ada
                    cartCountElement.textContent = data.cart_count || 0; // Menggunakan data.cart_count
                    // Tampilkan atau sembunyikan count jika 0
                    if (data.cart_count > 0) {
                        cartCountElement.style.display = 'flex'; // Atau 'block', 'inline-block'
                    } else {
                        cartCountElement.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error fetching cart count:', error));
    }

    // === Tambah Produk ke Keranjang (AJAX) ===
    if (addToCartButtons.length > 0) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', () => {
                const productId = button.dataset.productId;
                const productName = button.dataset.productName;
                const productPrice = parseFloat(button.dataset.productPrice);

                // Menggunakan URLSearchParams untuk body, lebih sesuai dengan form-urlencoded
                const formData = new URLSearchParams();
                formData.append('product_id', productId);
                formData.append('product_name', productName);
                formData.append('product_price', productPrice);
                formData.append('quantity', 1); // Default quantity saat menambah
                formData.append('add_to_cart', 1); // Menandakan request penambahan

                fetch('actions/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        // Mengubah Content-Type menjadi form-urlencoded
                        'Content-Type': 'application/x-www-form-urlencoded', 
                    },
                    body: formData, // Mengirim formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`${productName} berhasil ditambahkan ke keranjang!`);
                        updateCartCount(); // Perbarui jumlah keranjang setelah sukses
                    } else {
                        alert('Gagal menambahkan produk: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(error => {
                    console.error('Error adding to cart:', error);
                    alert('Terjadi kesalahan jaringan atau server.');
                });
            });
        });
    }

    // === Inisialisasi: Panggil fungsi untuk update jumlah keranjang saat DOM dimuat ===
    updateCartCount();

    // === Animasi Scroll (Intersection Observer) ===
    const sections = document.querySelectorAll('section');
    const observerOptions = {
        root: null, // Mengamati viewport
        rootMargin: '0px',
        threshold: 0.15 // Memicu ketika 15% dari elemen terlihat
    };

    const sectionObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible'); // Tambahkan kelas 'visible' untuk animasi
                observer.unobserve(entry.target); // Berhenti mengamati setelah elemen terlihat
            }
        });
    }, observerOptions);

    sections.forEach(section => {
        sectionObserver.observe(section);
    });
});