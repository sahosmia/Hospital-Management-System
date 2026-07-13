import React from 'react';
import { usePage } from '@inertiajs/react';
import { Bell, Search } from 'lucide-react';

export default function Header({ 
    title, 
    placeholder = "Search something...", 
    onSearch,
    searchValue 
}) {
    const { auth } = usePage().props;
    const user = auth?.user || { name: 'User' };

    return (
        <header className="sticky top-0 z-10 flex h-16 w-full items-center justify-between border-b border-gray-200 bg-white px-8 shadow-xs">
            <div className="flex items-center">
                <h1 className="text-xl font-semibold text-gray-900 capitalize">{title}</h1>
            </div>

            <div className="flex items-center space-x-6">
                {/* Search Bar */}
                {onSearch && (
                    <div className="relative w-64 max-w-xs">
                        <span className="absolute inset-y-0 left-0 flex items-center pl-3">
                            <Search className="h-4 w-4 text-gray-400" />
                        </span>
                        <input
                            type="text"
                            placeholder={placeholder}
                            value={searchValue}
                            onChange={(e) => onSearch(e.target.value)}
                            className="w-full pl-9 pr-4 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        />
                    </div>
                )}

                {/* Notifications icon */}
                <button className="relative rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-hidden">
                    <span className="sr-only">View notifications</span>
                    <Bell className="h-6 w-6" />
                    <span className="absolute top-1 right-1 block h-2.5 w-2.5 rounded-full bg-blue-500 ring-2 ring-white" />
                </button>

                {/* User avatar bar */}
                <div className="flex items-center border-l border-gray-200 pl-6">
                    <div className="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-700 uppercase">
                        {user.name.substring(0, 1)}
                    </div>
                    <span className="ml-3 text-sm font-semibold text-gray-800">{user.name}</span>
                </div>
            </div>
        </header>
    );
}
