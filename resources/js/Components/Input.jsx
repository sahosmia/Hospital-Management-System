import React from 'react';

export default function Input({
    label,
    name,
    type = 'text',
    value,
    onChange,
    error,
    placeholder = '',
    options = [], // for select type
    rows = 3, // for textarea type
    required = false,
    className = '',
    ...props
}) {
    const inputStyle = `w-full px-3 py-2 border rounded-md shadow-xs focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors ${
        error ? 'border-rose-500 bg-rose-50' : 'border-gray-300'
    }`;

    return (
        <div className={`mb-4 ${className}`}>
            {label && (
                <label className="block text-sm font-medium text-gray-700 mb-1" htmlFor={name}>
                    {label} {required && <span className="text-rose-500">*</span>}
                </label>
            )}

            {type === 'textarea' ? (
                <textarea
                    id={name}
                    name={name}
                    value={value}
                    onChange={onChange}
                    placeholder={placeholder}
                    rows={rows}
                    required={required}
                    className={inputStyle}
                    {...props}
                />
            ) : type === 'select' ? (
                <select
                    id={name}
                    name={name}
                    value={value}
                    onChange={onChange}
                    required={required}
                    className={inputStyle}
                    {...props}
                >
                    {placeholder && <option value="">{placeholder}</option>}
                    {options.map((opt) => (
                        <option key={opt.value} value={opt.value}>
                            {opt.label}
                        </option>
                    ))}
                </select>
            ) : (
                <input
                    id={name}
                    name={name}
                    type={type}
                    value={value}
                    onChange={onChange}
                    placeholder={placeholder}
                    required={required}
                    className={inputStyle}
                    {...props}
                />
            )}

            {error && (
                <p className="mt-1 text-xs text-rose-600 font-medium">{error}</p>
            )}
        </div>
    );
}
