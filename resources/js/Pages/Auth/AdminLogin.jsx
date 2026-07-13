import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import Button from '../../Components/Button';
import Input from '../../Components/Input';
import Toast from '../../Components/Toast';
import { HeartPulse } from 'lucide-react';

export default function AdminLogin() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [toast, setToast] = useState(null);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!email || !password) {
            setError('Both email and password are required');
            return;
        }

        setLoading(true);
        setError('');

        fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email, password }),
        })
        .then(res => res.json())
        .then(res => {
            setLoading(false);
            if (res.success) {
                // Save token locally
                localStorage.setItem('hms_token', res.data.token);
                setToast({
                    type: 'success',
                    message: `Welcome back, ${res.data.user.name}! Accessing staff dashboard...`,
                });
                
                const role = res.data.user.role;
                setTimeout(() => {
                    if (role === 'doctor') {
                        window.location.href = '/doctor/dashboard';
                    } else if (role === 'nurse') {
                        window.location.href = '/nurse/dashboard';
                    } else {
                        window.location.href = '/admin/dashboard';
                    }
                }, 1500);
            } else {
                setError(res.message || 'Invalid credentials. Please try again.');
            }
        })
        .catch(err => {
            setLoading(false);
            setError('Connection failed. Please try again.');
        });
    };

    return (
        <div className="flex min-h-screen">
            <Head title="Staff Sign In" />
            
            {/* Left Column: Visual branding */}
            <div className="hidden lg:flex w-1/2 bg-slate-900 text-white p-12 flex-col justify-between">
                <div className="flex items-center space-x-3">
                    <HeartPulse className="h-10 w-10 text-blue-400" />
                    <span className="text-xl font-bold tracking-wider uppercase text-blue-400">Jude General Hospital</span>
                </div>
                
                <div className="max-w-md">
                    <h1 className="text-4xl font-extrabold tracking-tight leading-tight">
                        Complete Hospital Operations Digitized
                    </h1>
                    <p className="mt-4 text-slate-300 text-base leading-relaxed">
                        Access patient admissions, bed management, medication tracking, inventory, surgery schedule, and reporting in real-time.
                    </p>
                </div>
                
                <div className="text-xs text-slate-500">
                    &copy; 2026 Jude Health. All rights reserved.
                </div>
            </div>

            {/* Right Column: Sign in form */}
            <div className="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
                <div className="w-full max-w-md space-y-8">
                    <div>
                        <h2 className="text-3xl font-bold tracking-tight text-gray-900">
                            Staff Sign In
                        </h2>
                        <p className="mt-2 text-sm text-gray-600">
                            Enter your email and password to access your role-specific dashboard
                        </p>
                    </div>

                    <form className="mt-8 space-y-6" onSubmit={handleSubmit}>
                        {error && (
                            <div className="p-3 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-md font-medium">
                                {error}
                            </div>
                        )}

                        <Input
                            label="Email Address"
                            name="email"
                            type="email"
                            placeholder="e.g. admin@hms.com"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            required
                        />

                        <Input
                            label="Security Password"
                            name="password"
                            type="password"
                            placeholder="••••••••"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            required
                        />

                        <div className="flex items-center justify-between text-sm">
                            <label className="flex items-center text-gray-600 font-medium">
                                <input type="checkbox" className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mr-2" />
                                Remember login
                            </label>
                            <a href="#" className="font-medium text-blue-600 hover:text-blue-500">
                                Forgot password?
                            </a>
                        </div>

                        <div>
                            <Button
                                type="submit"
                                variant="primary"
                                className="w-full"
                                disabled={loading}
                            >
                                {loading ? 'Signing In...' : 'Verify Staff Credentials'}
                            </Button>
                        </div>
                    </form>

                    <div className="text-center mt-4">
                        <a href="/auth/login" className="text-sm font-medium text-slate-600 hover:text-slate-500">
                            Are you a patient? Go back to Patient Access
                        </a>
                    </div>
                </div>
            </div>

            {toast && (
                <Toast
                    type={toast.type}
                    message={toast.message}
                    onClose={() => setToast(null)}
                />
            )}
        </div>
    );
}
