// Minimal JS for modal/lightbox and filtering
document.addEventListener('click', (e)=>{
  const t = e.target.closest('[data-open-art]');
  if (t){
    const id = t.dataset.openArt;
    const m = document.getElementById('art-modal-'+id);
    if (m){ m.classList.add('open'); }
  }
  if (e.target.matches('[data-close], .modal')){
    const modal = e.target.closest('.modal') || e.target;
    if (modal.classList.contains('modal')) modal.classList.remove('open');
  }
});
