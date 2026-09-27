/**
 * Open a native <dialog> as a modal from any element with `data-dialog-open="{dialog id}"`.
 *
 * Dialogs close with their own `<form method="dialog">` buttons, the Escape key, or a click on the backdrop.
 */
export function initDialogs(root = document) {
    root.querySelectorAll('[data-dialog-open]').forEach((trigger) => {
        const dialog = document.getElementById(trigger.dataset.dialogOpen);

        if (!(dialog instanceof HTMLDialogElement)) {
            return;
        }

        trigger.addEventListener('click', () => dialog.showModal());

        // A click on the dialog element itself (not its content) lands on the backdrop.
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    });
}
