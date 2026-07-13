import React from 'react';
import Button from './Button';

export default function Modal({
    isOpen = false,
    title,
    onClose,
    onConfirm,
    confirmText = 'Confirm',
    cancelText = 'Cancel',
    confirmVariant = 'primary',
    size = 'md', // sm, md, lg, xl
    children
}) {
    if (!isOpen) return null;

    const sizes = {
        sm: 'max-w-md',
        md: 'max-w-lg',
        lg: 'max-w-2xl',
        xl: 'max-w-5xl',
    };

    return (
        <div className="fixed inset-0 z-50 overflow-y-auto">
            {/* Backdrop */}
            <div
                className="fixed inset-0 bg-gray-500/75 backdrop-blur-xs transition-opacity"
                onClick={onClose}
            />

            <div className="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div className={`relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full ${sizes[size]}`}>
                    {/* Header */}
                    <div className="bg-gray-50 px-4 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 className="text-base font-semibold leading-6 text-gray-900">{title}</h3>
                        <button
                            onClick={onClose}
                            className="text-gray-400 hover:text-gray-500 focus:outline-hidden"
                        >
                            <span className="sr-only">Close</span>
                            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {/* Content */}
                    <div className="bg-white px-6 py-4">
                        {children}
                    </div>

                    {/* Footer */}
                    <div className="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        {onConfirm && (
                            <Button
                                variant={confirmVariant}
                                onClick={onConfirm}
                                className="sm:ml-3 w-full sm:w-auto mb-2 sm:mb-0"
                            >
                                {confirmText}
                            </Button>
                        )}
                        <Button
                            variant="outline"
                            onClick={onClose}
                            className="w-full sm:w-auto"
                        >
                            {cancelText}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    );
}
