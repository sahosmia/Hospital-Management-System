import React, { useEffect } from 'react';
import { CheckCircle2, AlertCircle, X, Info } from 'lucide-react';

export default function Toast({
    message,
    type = 'success', // success, error, info
    onClose,
    duration = 4000
}) {
    useEffect(() => {
        if (duration && onClose) {
            const timer = setTimeout(onClose, duration);
            return () => clearTimeout(timer);
        }
    }, [duration, onClose]);

    if (!message) return null;

    const styles = {
        success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
        error: 'bg-rose-50 border-rose-200 text-rose-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800',
    };

    const icons = {
        success: <CheckCircle2 className="h-5 w-5 text-emerald-600 mr-3" />,
        error: <AlertCircle className="h-5 w-5 text-rose-600 mr-3" />,
        info: <Info className="h-5 w-5 text-blue-600 mr-3" />,
    };

    return (
        <div className="fixed bottom-5 right-5 z-50 animate-bounce">
            <div className={`flex items-center p-4 border rounded-lg shadow-lg max-w-sm ${styles[type]}`}>
                {icons[type]}
                <span className="text-sm font-medium pr-8">{message}</span>
                {onClose && (
                    <button 
                        onClick={onClose} 
                        className="absolute right-3 top-4 text-gray-400 hover:text-gray-600 focus:outline-hidden"
                    >
                        <X className="h-4 w-4" />
                    </button>
                )}
            </div>
        </div>
    );
}
