import React from 'react';

export default function Pagination({
    links = [],
    onPageChange
}) {
    if (!links || links.length <= 3) return null;

    return (
        <div className="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 mt-6">
            <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <nav className="isolate inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
                        {links.map((link, idx) => (
                            <button
                                key={idx}
                                disabled={!link.url}
                                onClick={() => onPageChange && onPageChange(link.url)}
                                className={`relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 ${
                                    link.active
                                        ? 'z-10 bg-blue-600 text-white focus:outline-hidden'
                                        : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-hidden'
                                } ${!link.url ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </nav>
                </div>
            </div>
        </div>
    );
}
