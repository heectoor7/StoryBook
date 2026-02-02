/**
 * stories-modal.js
 * Lógica del modal de stories (avance automático, interacción por click)
 */

(function(){
    const STORY_DURATION = 5000; // 5 segundos
    let storyModalEl = null;
    let storyModal = null;
    let currentStoryIndex = 0;
    let storyTimer = null;
    let progressInterval = null;

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        storyModalEl = document.getElementById('storiesModal');
        
        if (storyModalEl && window.bootstrap) {
            storyModal = new bootstrap.Modal(storyModalEl);
            
            storyModalEl.addEventListener('hidden.bs.modal', clearTimers);
            
            // avanzar al click en la mitad derecha, retroceder en la mitad izquierda
            storyModalEl.addEventListener('click', function(e) {
                const rect = e.currentTarget.getBoundingClientRect();
                const x = e.clientX - rect.left;
                if (x > rect.width / 2) {
                    nextStory();
                } else {
                    prevStory();
                }
            });
        }
    });

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    window.openStoriesModal = function(index) {
        console.log('openStoriesModal called', index, 'storiesData:', window.storiesData);
        if (!window.storiesData || window.storiesData.length === 0) {
            console.warn('No stories data available');
            return;
        }
        if (!storyModal) {
            console.warn('Modal not initialized');
            return;
        }
        currentStoryIndex = index;
        showStory(currentStoryIndex);
        storyModal.show();
    }

    function showStory(idx) {
        clearTimers();
        const s = window.storiesData[idx];
        const companyEl = document.getElementById('storyCompany');
        const contentEl = document.getElementById('storyContent');
        const metaEl = document.getElementById('storyMeta');
        const progress = document.getElementById('storyProgress');

        if (companyEl) companyEl.innerText = s.company_name || '';
        
        // Insertar contenido formateado con imagen si existe
        if (contentEl) {
            const safe = escapeHtml(s.content || '');
            let html = '';
            
            if (s.image) {
                html += `<img src="${s.image}" alt="${s.company_name}" style="width:100%;max-height:400px;object-fit:cover;border-radius:8px;margin-bottom:12px;">`;
            }
            
            html += '<div class="story-content-wrapper">' + 
                    safe.replace(/\n/g, '<br>') + '</div>';
            
            contentEl.innerHTML = html;
        }
        
        if (metaEl) {
            metaEl.innerText = s.expires_at ? 'Expira: ' + new Date(s.expires_at).toLocaleString() : '';
        }
        
        if (progress) progress.style.width = '0%';

        const start = Date.now();
        progressInterval = setInterval(() => {
            const pct = Math.min(100, (Date.now() - start) / STORY_DURATION * 100);
            if (progress) progress.style.width = pct + '%';
        }, 100);

        storyTimer = setTimeout(() => {
            nextStory();
        }, STORY_DURATION);
    }

    function nextStory() {
        if (!window.storiesData) return;
        if (currentStoryIndex + 1 >= window.storiesData.length) {
            // cerrar modal al terminar
            if (storyModal) storyModal.hide();
        } else {
            currentStoryIndex++;
            showStory(currentStoryIndex);
        }
    }

    function prevStory() {
        if (!window.storiesData) return;
        if (currentStoryIndex - 1 < 0) return;
        currentStoryIndex--;
        showStory(currentStoryIndex);
    }

    function clearTimers() {
        if (storyTimer) {
            clearTimeout(storyTimer);
            storyTimer = null;
        }
        if (progressInterval) {
            clearInterval(progressInterval);
            progressInterval = null;
        }
    }
})();
