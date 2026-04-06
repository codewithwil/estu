let originalData = {};

const API = {
  async getHomeContent() {
    const res = await fetch(BASE_URL + "/process/homeDesc.php?action=get");
    return await res.json();
  },

  async saveHomeContent(data) {
    return fetch(BASE_URL + "/process/homeDesc.php?action=save", {
      method: "POST",
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
  }
};

const elements = {
  topLabel: document.getElementById('topLabel'),
  mainTitle: document.getElementById('mainTitle'),
  boldSubtitle: document.getElementById('boldSubtitle'),
  lightSubtitle: document.getElementById('lightSubtitle'),
  ctaText: document.getElementById('ctaText'),
  previewLabel: document.getElementById('previewLabel'),
  previewTitle: document.getElementById('previewTitle'),
  previewBold: document.getElementById('previewBold'),
  previewLight: document.getElementById('previewLight'),
  previewBtn: document.querySelector('.preview-btn'),
  form: document.getElementById('homeForm')
};

const counters = {
  labelCount: document.getElementById('labelCount'),
  titleCount: document.getElementById('titleCount'),
  boldCount: document.getElementById('boldCount'),
  lightCount: document.getElementById('lightCount'),
  ctaCount: document.getElementById('ctaCount')
};

function updatePreview() {
  elements.previewLabel.textContent = elements.topLabel.value || 'Event Organizer Bali';
  elements.previewTitle.textContent = elements.mainTitle.value || 'ESTU';
  elements.previewBold.textContent = elements.boldSubtitle.value || 'Designing Experiences, Not Just Events';
  elements.previewLight.textContent = elements.lightSubtitle.value || 'We turn ideas into memorable events...';
  
  const btnText = elements.ctaText.value || 'Explore';
  elements.previewBtn.innerHTML = `${btnText} <i class="fas fa-arrow-down"></i>`;
}

function updateCharCount(element, counterId, max) {
  const current = element.value.length;
  document.getElementById(counterId).textContent = current;
  
  if (current >= max) {
    element.style.borderColor = 'var(--red)';
  } else if (current >= max * 0.8) {
    element.style.borderColor = 'var(--amber)';
  } else {
    element.style.borderColor = '';
  }
}

elements.topLabel.addEventListener('input', () => {
  updatePreview();
  updateCharCount(elements.topLabel, 'labelCount', 50);
});

elements.mainTitle.addEventListener('input', () => {
  updatePreview();
  updateCharCount(elements.mainTitle, 'titleCount', 20);
});

elements.boldSubtitle.addEventListener('input', () => {
  updatePreview();
  updateCharCount(elements.boldSubtitle, 'boldCount', 100);
});

elements.lightSubtitle.addEventListener('input', () => {
  updatePreview();
  updateCharCount(elements.lightSubtitle, 'lightCount', 200);
});

elements.ctaText.addEventListener('input', () => {
  updatePreview();
  updateCharCount(elements.ctaText, 'ctaCount', 20);
});

elements.form.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const data = {
    topLabel: elements.topLabel.value,
    mainTitle: elements.mainTitle.value,
    boldSubtitle: elements.boldSubtitle.value,
    lightSubtitle: elements.lightSubtitle.value,
    ctaText: elements.ctaText.value
  };
  
  try {
    await API.saveHomeContent(data);
    
    showToast('success', 'Berhasil', 'Perubahan berhasil disimpan');

    originalData = { ...data };
  } catch (error) {
    showToast('error', 'Error', 'Gagal menyimpan perubahan');
  }
});

// Reset form
function resetForm() {
  if (confirm('Yakin ingin mengembalikan ke perubahan terakhir yang disimpan?')) {
    loadData();
    showToast('success', 'Berhasil', 'Form direset ke data tersimpan');
  }
}


async function loadData() {
  try {
    populateForm({
      topLabel: '',
      mainTitle: '',
      boldSubtitle: '',
      lightSubtitle: '',
      ctaText: ''
    });


    const res = await API.getHomeContent();

    if (res && res.topLabel !== undefined) {
      populateForm(res);
    } else {
      populateForm({
        topLabel: 'Event Organizer Bali',
        mainTitle: 'ESTU',
        boldSubtitle: 'Designing Experiences, Not Just Events',
        lightSubtitle: 'We turn ideas into memorable events through creative concepts and flawless execution.',
        ctaText: 'Explore'
      });
    }

    originalData = {
      topLabel: elements.topLabel.value,
      mainTitle: elements.mainTitle.value,
      boldSubtitle: elements.boldSubtitle.value,
      lightSubtitle: elements.lightSubtitle.value,
      ctaText: elements.ctaText.value
    };

  } catch (error) {
    console.error('Error loading data:', error);
    showToast('error', 'Error', 'Gagal memuat data dari server');
  }
}

function populateForm(data) {
  elements.topLabel.value = data.topLabel || '';
  elements.mainTitle.value = data.mainTitle || '';
  elements.boldSubtitle.value = data.boldSubtitle || '';
  elements.lightSubtitle.value = data.lightSubtitle || '';
  elements.ctaText.value = data.ctaText || '';
  
  updatePreview();
  
  updateCharCount(elements.topLabel, 'labelCount', 50);
  updateCharCount(elements.mainTitle, 'titleCount', 20);
  updateCharCount(elements.boldSubtitle, 'boldCount', 100);
  updateCharCount(elements.lightSubtitle, 'lightCount', 200);
  updateCharCount(elements.ctaText, 'ctaCount', 20);

  ['input'].forEach(event => {
    elements.topLabel.dispatchEvent(new Event(event));
    elements.mainTitle.dispatchEvent(new Event(event));
    elements.boldSubtitle.dispatchEvent(new Event(event));
    elements.lightSubtitle.dispatchEvent(new Event(event));
    elements.ctaText.dispatchEvent(new Event(event));
  });
}

function showToast(type, title, message) {
  const toast = document.getElementById('toast');
  const iconBox = document.getElementById('toastIcon');
  const titleEl = document.getElementById('toastTitle');
  const msgEl = document.getElementById('toastMessage');

  titleEl.textContent = title;
  msgEl.textContent = message;

  iconBox.className = 'toast-icon';
  
  if (type === 'success') {
    iconBox.classList.add('success');
    iconBox.innerHTML = '<i class="fas fa-check"></i>';
  } else {
    iconBox.classList.add('error');
    iconBox.innerHTML = '<i class="fas fa-times"></i>';
  }

  toast.classList.add('show');

  setTimeout(() => {
    toast.classList.remove('show');
  }, 3000);
}

document.getElementById('notificationBtn')?.addEventListener('click', (e) => {
  e.stopPropagation();
  document.getElementById('notificationDropdown').classList.toggle('show');
});

document.addEventListener('click', (e) => {
  if (!e.target.closest('.notification-wrapper')) {
    document.getElementById('notificationDropdown')?.classList.remove('show');
  }
});

loadData();