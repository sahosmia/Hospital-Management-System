import React from 'react';

export default function StatsCard({
    icon: Icon,
    number,
    label,
    trend, // e.g. "+12%" or "-5%"
    trendType = 'success', // success, danger, default
    className = ''
}) {
    const trendColors = {
        success: 'text-emerald-600 bg-emerald-50 border-emerald-100',
        danger: 'text-rose-600 bg-rose-50 border-rose-100',
        default: 'text-gray-600 bg-gray-50 border-gray-100',
    };

    return (
        <div className={`bg-white rounded-lg border border-gray-200 p-6 shadow-xs flex items-center justify-between ${className}`}>
            <div>
                <p className="text-sm font-medium text-gray-500 capitalize">{label}</p>
                <h3 className="text-2xl font-bold text-gray-900 mt-1">{number}</h3>
                {trend && (
                    <span className={`inline-flex items-center px-2 py-0.5 mt-2 rounded border text-xs font-semibold ${trendColors[trendType]}`}>
                        {trend}
                    </span>
                )}
            </div>
            {Icon && (
                <div className="p-3 bg-blue-50 text-blue-600 rounded-lg">
                    <Icon className="h-6 w-6" />
                </div>
            )}
        </div>
    );
}
