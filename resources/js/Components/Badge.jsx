import React from 'react';

export default function Badge({
    type = 'gray', // success, warning, error, info, gray
    children,
    className = ''
}) {
    const baseStyle = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';

    const colors = {
        success: 'bg-emerald-100 text-emerald-800',
        warning: 'bg-amber-100 text-warning-800 text-amber-800',
        error: 'bg-rose-100 text-rose-800',
        info: 'bg-blue-100 text-blue-800',
        gray: 'bg-gray-100 text-gray-800',
    };

    return (
        <span className={`${baseStyle} ${colors[type]} ${className}`}>
            {children}
        </span>
    );
}
