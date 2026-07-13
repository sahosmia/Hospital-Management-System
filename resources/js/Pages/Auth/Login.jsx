import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import Button from '../../Components/Button';
import Input from '../../Components/Input';
import Toast from '../../Components/Toast';
import { HeartPulse } from 'lucide-react';

export default function Login() {
    const [phone, setPhone] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [toast, setToast] = useState(null);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!phone) {
            setError('Phone number is required');
            return;
        }

        setLoading(true);
        setError('');

        // Standard fetch or axios call to simulated OTP
        fetch('/api/auth/otp/request', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone }),
        })
        .then(res => res.json())
        .then(res => {
            setLoading(false);
            if (res.success) {
                setToast({
                    type: 'success',
                    message: `OTP sent successfully! Use simulated OTP: ${res.data.otp}`,
                });
                setTimeout(() => {
                    // Navigate to OTP verification page
                    window.location.href = `/auth/otp-verify?phone=${encodeURIComponent(phone)}&otp=${res.data.otp}`;
                }, 2000);
            } else {
                setError(res.message || 'Failed to request OTP.');
            }
        })
        .catch(err => {
            setLoading(false);
            setError('Connection failed. Please try again.');
        });
    };

    return (
        <div className="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
            <Head title="Patient Sign In" />
            <div className="w-full max-w-md space-y-8 bg-white p-8 rounded-lg border border-gray-200 shadow-sm">
                <div className="text-center">
                    <HeartPulse className="mx-auto h-12 w-12 text-blue-600" />
                    <h2 className="mt-6 text-3xl font-bold tracking-tight text-gray-900">
                        Patient Portal
                    </h2>
                    <p className="mt-2 text-sm text-gray-600">
                        Enter your phone number to receive a secure login OTP
                    </p>
                </div>

                <form className="mt-8 space-y-6" onSubmit={handleSubmit}>
                    <Input
                        label="Phone Number"
                        name="phone"
                        type="tel"
                        placeholder="e.g. 5556667771"
                        value={phone}
                        onChange={(e) => setPhone(e.target.value)}
                        error={error}
                        required
                    />

                    <div>
                        <Button
                            type="submit"
                            variant="primary"
                            className="w-full"
                            disabled={loading}
                        >
                            {loading ? 'Sending OTP...' : 'Send Access OTP'}
                        </Button>
                    </div>
                </form>

                <div className="text-center mt-4">
                    <a href="/auth/admin-login" className="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Are you a staff member? Sign in here
                    </a>
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
