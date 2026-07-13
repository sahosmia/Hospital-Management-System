import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import Button from '../../Components/Button';
import Input from '../../Components/Input';
import Toast from '../../Components/Toast';
import { ShieldCheck } from 'lucide-react';

export default function OTPVerification() {
    const [phone, setPhone] = useState('');
    const [otp, setOtp] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [toast, setToast] = useState(null);

    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        setPhone(params.get('phone') || '');
        const autoOtp = params.get('otp');
        if (autoOtp) {
            setOtp(autoOtp);
            setToast({
                type: 'info',
                message: `Autofilled simulated OTP: ${autoOtp}`,
                duration: 6000
            });
        }
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!otp) {
            setError('Verification OTP is required');
            return;
        }

        setLoading(true);
        setError('');

        fetch('/api/auth/otp/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone, otp }),
        })
        .then(res => res.json())
        .then(res => {
            setLoading(false);
            if (res.success) {
                // Save token locally
                localStorage.setItem('hms_token', res.data.token);
                setToast({
                    type: 'success',
                    message: 'Authentication successful! Accessing Patient Portal...',
                });
                setTimeout(() => {
                    window.location.href = '/patient/dashboard';
                }, 1500);
            } else {
                setError(res.message || 'Verification failed. Please check the code.');
            }
        })
        .catch(err => {
            setLoading(false);
            setError('Connection failed. Please try again.');
        });
    };

    return (
        <div className="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
            <Head title="Verify OTP" />
            <div className="w-full max-w-md space-y-8 bg-white p-8 rounded-lg border border-gray-200 shadow-sm">
                <div className="text-center">
                    <ShieldCheck className="mx-auto h-12 w-12 text-blue-600" />
                    <h2 className="mt-6 text-3xl font-bold tracking-tight text-gray-900">
                        Enter Security Code
                    </h2>
                    <p className="mt-2 text-sm text-gray-600">
                        A verification code has been dispatched to <span className="font-semibold">{phone}</span>
                    </p>
                </div>

                <form className="mt-8 space-y-6" onSubmit={handleSubmit}>
                    <Input
                        label="6-Digit Verification Code"
                        name="otp"
                        type="text"
                        placeholder="e.g. 123456"
                        maxLength={6}
                        value={otp}
                        onChange={(e) => setOtp(e.target.value)}
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
                            {loading ? 'Verifying...' : 'Verify & Enter Portal'}
                        </Button>
                    </div>
                </form>

                <div className="text-center mt-4">
                    <button
                        onClick={() => window.history.back()}
                        className="text-sm font-medium text-blue-600 hover:text-blue-500 bg-transparent border-0 cursor-pointer"
                    >
                        Go back and re-enter phone
                    </button>
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
