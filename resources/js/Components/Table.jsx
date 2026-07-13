import React from 'react';

export default function Table({
    headers = [],
    zebra = true,
    className = '',
    children
}) {
    return (
        <div className={`overflow-x-auto border border-gray-200 rounded-lg ${className}`}>
            <table className="min-w-full divide-y divide-gray-200 text-sm text-left">
                <thead className="bg-gray-50 text-gray-700 uppercase text-xs font-semibold tracking-wider">
                    <tr>
                        {headers.map((h, i) => (
                            <th key={i} className="px-6 py-3">
                                {h}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-200 text-gray-800">
                    {children}
                </tbody>
            </table>
        </div>
    );
}
