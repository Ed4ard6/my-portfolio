document.addEventListener('DOMContentLoaded', () => {
  const dialog = document.getElementById('confirmDialog');
  if (!dialog) return;

  const titleEl = dialog.querySelector('[data-confirm-title]');
  const messageEl = dialog.querySelector('[data-confirm-message]');
  const confirmBtn = dialog.querySelector('[data-confirm-yes]');
  const cancelBtn = dialog.querySelector('[data-confirm-no]');

  document.querySelectorAll('[data-confirm-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      event.preventDefault();

      const title = form.dataset.confirmTitle || 'Confirmar acción';
      const message = form.dataset.confirmMessage || '¿Estás seguro de continuar?';

      if (titleEl) titleEl.textContent = title;
      if (messageEl) messageEl.textContent = message;

      const closeDialog = () => {
        dialog.close();
      };

      const confirmHandler = () => {
        dialog.close();
        form.submit();
      };

      confirmBtn.addEventListener('click', confirmHandler, { once: true });
      cancelBtn.addEventListener('click', closeDialog, { once: true });
      dialog.addEventListener('close', closeDialog, { once: true });

      dialog.showModal();
    });
  });
});
