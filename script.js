/**
 * =========================================================================
 * IBNU & ADINDA WEDDING INVITATION - INTERACTIVE LOGIC & AUTO-SCROLL ENGINE
 * =========================================================================
 *
 * PANDUAN PENGEMBANG (UNTUK JUNIOR PROGRAMMER / MODEL AI):
 * Anda dapat dengan mudah mengubah Nama Mempelai, Foto, Tanggal, dan Rekening
 * melalui konfigurasi objek WEDDING_CONFIG di bawah ini.
 */

const WEDDING_CONFIG = {
  // --- NAMA & FOTO MEMPELAI (DUMMY/RANDOM) ---
  groom: {
    fullName: "Raden Ibnu Pratama, S.Kom.",
    nickName: "Ibnu",
    parents: "Bpk. Bambang Sulistyo & Ibu Siti Rahmawati",
    instagram: "@ibnu_pratama",
    photoUrl: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80"
  },
  bride: {
    fullName: "Putri Adinda Larasati, S.M.",
    nickName: "Adinda",
    parents: "Bpk. Hartono Wibowo & Ibu Sri Handayani",
    instagram: "@adinda_larasati",
    photoUrl: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80"
  },

  // --- TANGGAL ACARA (UNTUK COUNTDOWN) ---
  eventDate: new Date("2026-10-24T08:00:00+07:00"),

  // --- PENGATURAN AUTO-SCROLL PERLAHAN (SLOW SCROLL) ---
  autoScroll: {
    speedPixelsPerFrame: 1.2, // Kecepatan scroll perlahan (1.0 - 1.5 px/frame = halus dan lambat)
    targetElementId: "rsvp-form" // Elemen tujuan akhir auto-scroll
  }
};

/* =========================================================
   1. AUTO-SCROLL ENGINE (REQUEST ANIMATION FRAME)
   ========================================================= */
let isAutoScrolling = false;
let autoScrollAnimationId = null;
let userManuallyInterrupted = false;

/**
 * Memulai animasi scroll perlahan menuju ke Form Kehadiran Tamu (RSVP)
 */
function startSlowAutoScroll() {
  const targetElement = document.getElementById(WEDDING_CONFIG.autoScroll.targetElementId);
  if (!targetElement) return;

  isAutoScrolling = true;
  userManuallyInterrupted = false;
  updateAutoScrollUI(true, "Menggulir otomatis secara perlahan ke Form Kehadiran...");

  function scrollStep() {
    if (!isAutoScrolling) return;

    // Posisi target terhadap viewport
    const targetRect = targetElement.getBoundingClientRect();
    const targetMiddle = targetRect.top;

    // Jika form RSVP sudah masuk dan berada di tengah atas viewport (<= 120px dari top)
    if (targetMiddle <= 120) {
      stopAutoScroll(true);
      return;
    }

    // Gerakkan scroll secara bertahap
    window.scrollBy({
      top: WEDDING_CONFIG.autoScroll.speedPixelsPerFrame,
      left: 0,
      behavior: "instant"
    });

    autoScrollAnimationId = requestAnimationFrame(scrollStep);
  }

  // Batalkan animasi sebelumnya jika masih berjalan
  if (autoScrollAnimationId) cancelAnimationFrame(autoScrollAnimationId);
  autoScrollAnimationId = requestAnimationFrame(scrollStep);
}

/**
 * Menghentikan auto scroll
 * @param {boolean} reachedDestination - Apakah berhenti karena sudah sampai di RSVP
 */
function stopAutoScroll(reachedDestination = false) {
  isAutoScrolling = false;
  if (autoScrollAnimationId) {
    cancelAnimationFrame(autoScrollAnimationId);
    autoScrollAnimationId = null;
  }

  const targetElement = document.getElementById(WEDDING_CONFIG.autoScroll.targetElementId);

  if (reachedDestination) {
    updateAutoScrollUI(false, "Sampai di Form Kehadiran!");
    const banner = document.getElementById("autoscroll-banner");
    if (banner) banner.classList.add("hide");

    // Efek highlight berkilau pada form RSVP
    const rsvpWrapper = document.querySelector(".rsvp-wrapper");
    if (rsvpWrapper) {
      rsvpWrapper.classList.add("highlight-focus");
      setTimeout(() => rsvpWrapper.classList.remove("highlight-focus"), 6000);
    }

    showToast("✨ Selamat datang di Form Konfirmasi Kehadiran!");
  } else {
    updateAutoScrollUI(false, "Auto-scroll dijeda (Klik untuk lanjut)");
  }
}

/**
 * Update tombol status dan banner auto-scroll
 */
function updateAutoScrollUI(active, message) {
  const btnToggle = document.getElementById("btn-toggle-autoscroll");
  const tooltip = document.getElementById("autoscroll-tooltip");
  const banner = document.getElementById("autoscroll-banner");
  const bannerText = document.getElementById("autoscroll-banner-text");
  const pauseBtn = document.getElementById("btn-pause-autoscroll");

  if (btnToggle) {
    if (active) {
      btnToggle.classList.add("active");
      if (tooltip) tooltip.textContent = "Jeda Auto-Scroll";
    } else {
      btnToggle.classList.remove("active");
      if (tooltip) tooltip.textContent = "Lanjutkan Auto-Scroll";
    }
  }

  if (banner && bannerText) {
    bannerText.textContent = message;
    if (active) {
      banner.classList.remove("hide");
      if (pauseBtn) pauseBtn.textContent = "Jeda";
    } else {
      if (userManuallyInterrupted) {
        banner.classList.remove("hide");
        if (pauseBtn) pauseBtn.textContent = "Lanjut";
      }
    }
  }
}

// Deteksi interaksi manual pengguna untuk pause auto-scroll secara sopan
function handleUserScrollInteraction() {
  if (isAutoScrolling) {
    userManuallyInterrupted = true;
    stopAutoScroll(false);
  }
}

window.addEventListener("wheel", handleUserScrollInteraction, { passive: true });
window.addEventListener("touchmove", handleUserScrollInteraction, { passive: true });
window.addEventListener("keydown", (e) => {
  if (["ArrowDown", "ArrowUp", "PageDown", "PageUp", "Space"].includes(e.code)) {
    handleUserScrollInteraction();
  }
}, { passive: true });

/* =========================================================
   2. COVER / OPEN INVITATION BUTTON
   ========================================================= */
document.addEventListener("DOMContentLoaded", () => {
  // Tangkap query param 'to' untuk nama tamu personalisasi
  const urlParams = new URLSearchParams(window.location.search);
  const guestParam = urlParams.get("to");
  if (guestParam) {
    const guestElem = document.getElementById("guest-name");
    if (guestElem) guestElem.textContent = decodeURIComponent(guestParam.replace(/\+/g, " "));
  }

  // Inisialisasi daftar ucapan tamu (localStorage)
  renderWishes();

  // Inisialisasi countdown
  initCountdownTimer();

  // Inisialisasi canvas partikel
  initSparkles();

  // Tombol Buka Undangan
  const btnOpen = document.getElementById("btn-open-invitation");
  const coverScreen = document.getElementById("wedding-cover");
  const floatingControls = document.getElementById("floating-controls");

  if (btnOpen && coverScreen) {
    btnOpen.addEventListener("click", () => {
      // Buka cover
      coverScreen.classList.add("opened");

      // Munculkan floating controls
      if (floatingControls) {
        floatingControls.classList.remove("hide");
      }

      // Mulai background music otomatis
      startRomanticAudio();

      // Mulai auto-scroll perlahan menuju ke Form RSVP
      setTimeout(() => {
        startSlowAutoScroll();
      }, 700);
    });
  }

  // Tombol Toggle Auto-Scroll
  const btnToggleAuto = document.getElementById("btn-toggle-autoscroll");
  if (btnToggleAuto) {
    btnToggleAuto.addEventListener("click", () => {
      if (isAutoScrolling) {
        userManuallyInterrupted = true;
        stopAutoScroll(false);
      } else {
        startSlowAutoScroll();
      }
    });
  }

  const btnPauseBanner = document.getElementById("btn-pause-autoscroll");
  if (btnPauseBanner) {
    btnPauseBanner.addEventListener("click", () => {
      if (isAutoScrolling) {
        userManuallyInterrupted = true;
        stopAutoScroll(false);
      } else {
        startSlowAutoScroll();
      }
    });
  }

  // Tombol Toggle Musik
  const btnMusic = document.getElementById("btn-music");
  if (btnMusic) {
    btnMusic.addEventListener("click", toggleMusic);
  }

  // Tombol Salin No Rekening
  document.querySelectorAll(".btn-copy").forEach(btn => {
    btn.addEventListener("click", (e) => {
      const textToCopy = e.currentTarget.getAttribute("data-copy");
      if (textToCopy) {
        navigator.clipboard.writeText(textToCopy).then(() => {
          showToast(`✓ Nomor rekening ${textToCopy} berhasil disalin!`);
        }).catch(() => {
          showToast("Gagal menyalin nomor rekening.");
        });
      }
    });
  });

  // Inisialisasi Galeri Slideshow Perlahan
  initGallerySlideshow();

  // Form RSVP Handler
  initRSVPForm();
});

/* =========================================================
   3. ROMANTIC BACKGROUND MUSIC (WEB AUDIO API SYNTHESIZER)
   ========================================================= */
let audioCtx = null;
let isMusicPlaying = false;
let melodyInterval = null;

function startRomanticAudio() {
  if (isMusicPlaying) return;
  try {
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    if (!AudioContext) return;
    
    if (!audioCtx) {
      audioCtx = new AudioContext();
    }
    
    if (audioCtx.state === "suspended") {
      audioCtx.resume();
    }

    isMusicPlaying = true;
    updateMusicUI(true);

    // Ethereal romantic harp/bell sequence in F major / D minor
    const notes = [
      261.63, 329.63, 392.00, 523.25, // C Major chord
      293.66, 349.23, 440.00, 587.33, // D Minor chord
      261.63, 329.63, 392.00, 659.25, // E chord
      349.23, 440.00, 523.25, 698.46  // F chord
    ];
    let noteIdx = 0;

    function playSoftHarpNote(freq) {
      if (!audioCtx || !isMusicPlaying) return;
      
      const osc = audioCtx.createOscillator();
      const gainNode = audioCtx.createGain();

      osc.type = "sine";
      osc.frequency.setValueAtTime(freq, audioCtx.currentTime);

      // Volume envelope: soft gentle pluck and long decay
      gainNode.gain.setValueAtTime(0.001, audioCtx.currentTime);
      gainNode.gain.exponentialRampToValueAtTime(0.08, audioCtx.currentTime + 0.1);
      gainNode.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 2.5);

      osc.connect(gainNode);
      gainNode.connect(audioCtx.destination);

      osc.start();
      osc.stop(audioCtx.currentTime + 2.6);
    }

    melodyInterval = setInterval(() => {
      if (isMusicPlaying) {
        playSoftHarpNote(notes[noteIdx]);
        noteIdx = (noteIdx + 1) % notes.length;
      }
    }, 700);

  } catch (err) {
    console.warn("Audio Context init note:", err);
  }
}

function toggleMusic() {
  if (isMusicPlaying) {
    isMusicPlaying = false;
    if (melodyInterval) clearInterval(melodyInterval);
    updateMusicUI(false);
  } else {
    startRomanticAudio();
  }
}

function updateMusicUI(playing) {
  const btnMusic = document.getElementById("btn-music");
  if (!btnMusic) return;
  const icon = btnMusic.querySelector("i");
  if (playing) {
    btnMusic.classList.add("active");
    if (icon) icon.classList.add("rotating");
  } else {
    btnMusic.classList.remove("active");
    if (icon) icon.classList.remove("rotating");
  }
}

/* =========================================================
   4. COUNTDOWN TIMER REALTIME
   ========================================================= */
function initCountdownTimer() {
  const daysEl = document.getElementById("timer-days");
  const hoursEl = document.getElementById("timer-hours");
  const minutesEl = document.getElementById("timer-minutes");
  const secondsEl = document.getElementById("timer-seconds");

  function update() {
    const now = new Date().getTime();
    const distance = WEDDING_CONFIG.eventDate.getTime() - now;

    if (distance < 0) {
      if (daysEl) daysEl.textContent = "00";
      if (hoursEl) hoursEl.textContent = "00";
      if (minutesEl) minutesEl.textContent = "00";
      if (secondsEl) secondsEl.textContent = "00";
      return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    if (daysEl) daysEl.textContent = String(days).padStart(2, "0");
    if (hoursEl) hoursEl.textContent = String(hours).padStart(2, "0");
    if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, "0");
    if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, "0");
  }

  update();
  setInterval(update, 1000);
}

/* =========================================================
   5. RSVP & LIVE WISHES WALL (LOCAL STORAGE)
   ========================================================= */
const DEFAULT_WISHES = [
  {
    name: "H. Muhammad Ridwan & Keluarga",
    status: "Hadir",
    pax: "2",
    message: "Barakallahu lakuma wa baraka 'alaikuma wa jama'a bainakuma fii khoir. Semoga Ibnu & Adinda menjadi keluarga yang sakinah, mawaddah, warahmah. Aamiin!",
    time: "1 jam yang lalu"
  },
  {
    name: "Clarissa Dewi, S.I.Kom",
    status: "Hadir",
    pax: "1",
    message: "Selamat untuk Adinda dan Mas Ibnu! Lancar sampai hari H yaa cantik, so happy for both of you! 🥰✨",
    time: "3 jam yang lalu"
  },
  {
    name: "Doni Prasetyo",
    status: "Masih Ragu",
    pax: "1",
    message: "Selamat menempuh hidup baru bro Ibnu! Diusahakan banget bisa hadir ya bro.",
    time: "5 jam yang lalu"
  }
];

function getWishes() {
  const stored = localStorage.getItem("wedding_wishes");
  if (stored) {
    try {
      return JSON.parse(stored);
    } catch {
      return DEFAULT_WISHES;
    }
  }
  return DEFAULT_WISHES;
}

function saveWishes(wishes) {
  localStorage.setItem("wedding_wishes", JSON.stringify(wishes));
}

async function renderWishes() {
  const wishesList = document.getElementById("wishes-list");
  const countEl = document.getElementById("wishes-count");
  if (!wishesList) return;

  try {
    const response = await fetch("api/get_wishes.php");
    if (response.ok) {
      const result = await response.json();
      if (result.success && Array.isArray(result.data)) {
        displayWishesList(result.data);
        if (countEl) countEl.textContent = result.count ?? result.data.length;
        return;
      }
    }
  } catch (err) {
    console.info("Fetching wishes from API failed, using fallback storage:", err);
  }

  // Fallback ke LocalStorage jika API tidak tersedia
  const wishes = getWishes();
  if (countEl) countEl.textContent = wishes.length;
  displayWishesList(wishes);
}

function displayWishesList(wishes) {
  const wishesList = document.getElementById("wishes-list");
  if (!wishesList) return;
  wishesList.innerHTML = "";

  wishes.forEach(item => {
    let badgeClass = "badge-hadir";
    let badgeText = "Hadir";

    if (item.status === "Tidak Hadir") {
      badgeClass = "badge-tidakhadir";
      badgeText = "Berhalangan";
    } else if (item.status === "Masih Ragu") {
      badgeClass = "badge-ragu";
      badgeText = "Ragu-ragu";
    }

    const firstLetter = item.name ? item.name.trim().charAt(0).toUpperCase() : "T";

    const itemEl = document.createElement("div");
    itemEl.className = "wish-item";
    itemEl.innerHTML = `
      <div class="wish-header">
        <div class="wish-sender-info">
          <div class="wish-avatar">${firstLetter}</div>
          <span class="wish-sender-name">${escapeHTML(item.name)}</span>
        </div>
        <span class="wish-badge ${badgeClass}">${badgeText}</span>
      </div>
      <p class="wish-text">${escapeHTML(item.message)}</p>
      <span class="wish-time"><i class="fa-regular fa-clock"></i> ${item.time || "Baru saja"}</span>
    `;

    wishesList.appendChild(itemEl);
  });
}

function initRSVPForm() {
  const form = document.getElementById("form-rsvp");
  const submitBtn = document.getElementById("btn-submit-rsvp");
  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const name = document.getElementById("rsvp-name").value.trim();
    const status = document.getElementById("rsvp-status").value;
    const pax = document.getElementById("rsvp-pax").value;
    const message = document.getElementById("rsvp-message").value.trim();

    if (!name || !status || !message) {
      showToast("Mohon lengkapi semua kolom formulir!");
      return;
    }

    const originalBtnHTML = submitBtn ? submitBtn.innerHTML : "";
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Mengirim Konfirmasi...';
    }

    try {
      const res = await fetch("api/save_rsvp.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, status, pax, message })
      });

      const result = await res.json();

      if (result.success && result.data) {
        form.reset();
        showToast("🎉 Terima kasih atas ucapan dan konfirmasi kehadiran Anda!");
        // Re-render daftar ucapan terbaru dari database
        renderWishes();
      } else {
        throw new Error(result.message || "Gagal menyimpan");
      }
    } catch (err) {
      console.warn("API save error, fallback to local storage:", err);
      // Fallback ke LocalStorage jika offline
      const newWish = { name, status, pax, message, time: "Baru saja" };
      const wishes = getWishes();
      wishes.unshift(newWish);
      saveWishes(wishes);
      displayWishesList(wishes);
      const countEl = document.getElementById("wishes-count");
      if (countEl) countEl.textContent = wishes.length;

      form.reset();
      showToast("🎉 Terima kasih atas ucapan dan konfirmasi kehadiran Anda!");
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnHTML;
      }
    }
  });
}

function escapeHTML(str) {
  return str.replace(/[&<>'"]/g, 
    tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
  );
}

/* =========================================================
   6. SINGLE-IMAGE SLOW SLIDESHOW GALLERY ENGINE
   ========================================================= */
function initGallerySlideshow() {
  const container = document.getElementById("gallery-slideshow");
  const slides = document.querySelectorAll(".slide-item");
  const dots = document.querySelectorAll(".slide-dot");
  const prevBtn = document.getElementById("btn-slide-prev");
  const nextBtn = document.getElementById("btn-slide-next");
  const counter = document.getElementById("slide-counter");

  if (!slides || slides.length === 0) return;

  let currentIndex = 0;
  let slideInterval = null;
  const slideDuration = 5000; // Pergantian perlahan setiap 5 detik

  function updateSlide(newIndex) {
    slides[currentIndex].classList.remove("active");
    if (dots[currentIndex]) dots[currentIndex].classList.remove("active");

    currentIndex = (newIndex + slides.length) % slides.length;

    slides[currentIndex].classList.add("active");
    if (dots[currentIndex]) dots[currentIndex].classList.add("active");

    if (counter) {
      counter.textContent = `${currentIndex + 1} / ${slides.length}`;
    }
  }

  function nextSlide() {
    updateSlide(currentIndex + 1);
  }

  function prevSlide() {
    updateSlide(currentIndex - 1);
  }

  function startAutoPlay() {
    if (slideInterval) clearInterval(slideInterval);
    slideInterval = setInterval(nextSlide, slideDuration);
  }

  function pauseAutoPlay() {
    if (slideInterval) {
      clearInterval(slideInterval);
      slideInterval = null;
    }
  }

  // Event Listeners
  if (nextBtn) {
    nextBtn.addEventListener("click", () => {
      nextSlide();
      startAutoPlay();
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", () => {
      prevSlide();
      startAutoPlay();
    });
  }

  dots.forEach((dot, idx) => {
    dot.addEventListener("click", () => {
      updateSlide(idx);
      startAutoPlay();
    });
  });

  // Jeda saat hover agar pengguna nyaman menikmati foto
  if (container) {
    container.addEventListener("mouseenter", pauseAutoPlay);
    container.addEventListener("mouseleave", startAutoPlay);
    container.addEventListener("touchstart", pauseAutoPlay, { passive: true });
    container.addEventListener("touchend", startAutoPlay, { passive: true });
  }

  startAutoPlay();
}

/* =========================================================
   7. TOAST NOTIFICATION UTILITY
   ========================================================= */
function showToast(message) {
  const container = document.getElementById("toast-container");
  if (!container) return;

  const toast = document.createElement("div");
  toast.className = "toast-item";
  toast.innerHTML = `<i class="fa-solid fa-circle-info"></i> <span>${message}</span>`;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = "0";
    toast.style.transform = "translateX(100%)";
    toast.style.transition = "all 0.4s ease";
    setTimeout(() => toast.remove(), 400);
  }, 4000);
}

/* =========================================================
   8. FLOATING SPARKLE CANVAS EFFECT
   ========================================================= */
function initSparkles() {
  const canvas = document.getElementById("sparkle-canvas");
  if (!canvas) return;

  const ctx = canvas.getContext("2d");
  let width = (canvas.width = window.innerWidth);
  let height = (canvas.height = window.innerHeight);

  window.addEventListener("resize", () => {
    width = canvas.width = window.innerWidth;
    height = canvas.height = window.innerHeight;
  });

  const particles = [];
  const particleCount = 28;

  for (let i = 0; i < particleCount; i++) {
    particles.push({
      x: Math.random() * width,
      y: Math.random() * height,
      radius: Math.random() * 2 + 1,
      speedY: -(Math.random() * 0.4 + 0.2),
      speedX: (Math.random() - 0.5) * 0.3,
      alpha: Math.random() * 0.5 + 0.3,
      alphaChange: (Math.random() - 0.5) * 0.01
    });
  }

  function render() {
    ctx.clearRect(0, 0, width, height);

    particles.forEach(p => {
      p.y += p.speedY;
      p.x += p.speedX;
      p.alpha += p.alphaChange;

      if (p.alpha <= 0.1 || p.alpha >= 0.7) {
        p.alphaChange = -p.alphaChange;
      }

      if (p.y < 0) {
        p.y = height + 10;
        p.x = Math.random() * width;
      }

      ctx.beginPath();
      ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(212, 175, 55, ${p.alpha})`;
      ctx.fill();
    });

    requestAnimationFrame(render);
  }

  render();
}
