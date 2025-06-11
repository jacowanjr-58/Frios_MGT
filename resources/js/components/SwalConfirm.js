/**
 * SweetAlert2 confirmation dialog component
 * @param {Object} options - Configuration options
 * @param {string} options.formSelector - CSS selector for the form
 * @param {string} options.triggerSelector - CSS selector for the trigger element
 * @param {string} options.title - Dialog title
 * @param {string} options.text - Dialog text
 * @param {string} options.icon - Dialog icon
 * @param {string} options.confirmButtonText - Text for confirm button
 */
export const initSwalConfirm = (options = {}) => {
    const {
        formSelector = 'form',
        triggerSelector = '.delete-trigger',
        title = 'Are you sure?',
        text = "You won't be able to revert this!",
        icon = 'warning',
        confirmButtonText = 'Yes, delete it!'
    } = options;

    document.querySelectorAll(triggerSelector).forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const form = trigger.closest(formSelector);
            
            if (!form) {
                console.error('No form found for the trigger');
                return;
            }

            Swal.fire({
                title,
                text,
                icon,
                showCancelButton: true,
                confirmButtonColor: '#00ABC7',
                cancelButtonColor: '#FF3131',
                confirmButtonText,
                customClass: {
                    confirmButton: 'swal2-confirm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
}; 