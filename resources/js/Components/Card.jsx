import React from 'react';

export default function Card({
    variant = 'default', // default, hover, selected
    className = '',
    onClick,
    children,
    ...props
}) {
    const baseStyle = 'bg-white rounded-lg border border-gray-200 overflow-hidden';
    
    const variants = {
        default: 'shadow-xs',
        hover: 'shadow-xs hover:shadow-md hover:border-gray-300 transition-all cursor-pointer',
        selected: 'ring-2 ring-blue-500 shadow-md',
    };

    return (
        <div
            onClick={onClick}
            className={`${baseStyle} ${variants[variant]} ${className}`}
            {...props}
        >
            {children}
        </div>
    );
}
