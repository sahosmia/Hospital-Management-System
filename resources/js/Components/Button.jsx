import React from 'react';

export default function Button({
    type = 'button',
    variant = 'primary', // primary, secondary, danger, success, outline, gray
    size = 'md', // sm, md, lg
    className = '',
    disabled = false,
    onClick,
    children,
    ...props
}) {
    const baseStyle = 'inline-flex items-center justify-center font-medium rounded-md shadow-xs transition-colors focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:opacity-50 disabled:cursor-not-allowed';

    const variants = {
        primary: 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500',
        secondary: 'bg-indigo-600 hover:bg-indigo-700 text-white focus:ring-indigo-500',
        success: 'bg-emerald-600 hover:bg-emerald-700 text-white focus:ring-emerald-500',
        danger: 'bg-rose-600 hover:bg-rose-700 text-white focus:ring-rose-500',
        outline: 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-blue-500',
        gray: 'bg-gray-100 hover:bg-gray-200 text-gray-800 focus:ring-gray-500',
    };

    const sizes = {
        sm: 'px-3 py-1.5 text-xs',
        md: 'px-4 py-2 text-sm',
        lg: 'px-5 py-2.5 text-base',
    };

    return (
        <button
            type={type}
            disabled={disabled}
            onClick={onClick}
            className={`${baseStyle} ${variants[variant]} ${sizes[size]} ${className}`}
            {...props}
        >
            {children}
        </button>
    );
}
