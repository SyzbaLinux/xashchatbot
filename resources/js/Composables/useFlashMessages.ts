import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import Swal from 'sweetalert2';

export function useFlashMessages() {
    const page = usePage();

    // Watch for flash messages from backend
    watch(
        () => page.props.flash,
        (flash: any) => {
            if (!flash) return;

            // Success message
            if (flash.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: flash.success,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'colored-toast'
                    }
                });
            }

            // Error message
            if (flash.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: flash.error,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'colored-toast'
                    }
                });
            }

            // Warning message
            if (flash.warning) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: flash.warning,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'colored-toast'
                    }
                });
            }

            // Info message
            if (flash.info) {
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: flash.info,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'colored-toast'
                    }
                });
            }
        },
        { deep: true, immediate: true }
    );

    // Helper functions for programmatic alerts
    const showSuccess = (message: string, duration: number = 3000) => {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: duration,
            timerProgressBar: true,
            customClass: {
                popup: 'colored-toast'
            }
        });
    };

    const showError = (message: string, duration: number = 4000) => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: duration,
            timerProgressBar: true,
            customClass: {
                popup: 'colored-toast'
            }
        });
    };

    const showWarning = (message: string, duration: number = 3500) => {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: duration,
            timerProgressBar: true,
            customClass: {
                popup: 'colored-toast'
            }
        });
    };

    const showInfo = (message: string, duration: number = 3000) => {
        Swal.fire({
            icon: 'info',
            title: 'Info',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: duration,
            timerProgressBar: true,
            customClass: {
                popup: 'colored-toast'
            }
        });
    };

    return {
        showSuccess,
        showError,
        showWarning,
        showInfo
    };
}

// Helper function to show custom alerts
export function showAlert(
    type: 'success' | 'error' | 'warning' | 'info' | 'question',
    title: string,
    text?: string,
    options?: any
) {
    return Swal.fire({
        icon: type,
        title: title,
        text: text,
        ...options
    });
}

// Helper function to show confirmation dialog
export function showConfirm(
    title: string = 'Are you sure?',
    text: string = 'This action cannot be undone',
    confirmButtonText: string = 'Yes, proceed!',
    cancelButtonText: string = 'Cancel'
) {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        reverseButtons: true
    });
}

// Helper function to show toast notification
export function showToast(
    type: 'success' | 'error' | 'warning' | 'info',
    message: string,
    duration: number = 3000
) {
    Swal.fire({
        icon: type,
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: duration,
        timerProgressBar: true,
        customClass: {
            popup: 'colored-toast'
        }
    });
}
